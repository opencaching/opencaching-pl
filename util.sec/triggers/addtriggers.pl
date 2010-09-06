#!/usr/bin/perl 

# addtriggers
#
# Device to add triggers on demand in databases assigned
# to users without SUPER privilege.
#
# It is intended to be run as a cron job from a user with
# SUPER privileges and full access to all the concerned databases.
# 
# Set username and password for this user in a ~/.my.cnf file
# under the label [trigger_creator]
# 
# See the docs at http://datacharmer.blogspot.com/

package main;
use strict;
use warnings;
use English qw( -no_match_vars );
use Data::Dumper;
use DBI;
use Carp;

our $VERSION    = '0.2';
our $DEBUG      = $ENV{DEBUG};

my $dbh=DBI->connect('dbi:mysql:stardata1;host=localhost'
            . ";mysql_read_default_file=/home/openc11/.my.cnf"
            . ';mysql_read_default_group=trigger_creator',
                undef,
                undef,
                {RaiseError => 1, PrintError=> 0}) 
         or die "Can't connect: $DBI::errstr\n"; 

my $database_list_query = qq{
    SELECT DISTINCT 
        table_schema 
    FROM 
        information_schema.tables 
    WHERE 
        table_name ='trigger_request'};

my $databases = safe_selectcol_arrayref($dbh, $database_list_query,undef);

for my $db (@$databases) {
    use_db($dbh,$db);
    # 
    # no need to check anymore: the initial query ensures that
    # only databases with a trigger_request tables are inspected
    # 
    # my ($trigger_request) = $dbh->selectrow_array(
    #    qq{SHOW TABLES LIKE 'trigger_request'});
    # next unless $trigger_request;
    
    # 
    # create the trigger_answer table
    # 
    safe_do(
            $dbh, 
            qq{create table if not exists trigger_answer 
                (trigger_name varchar(50) not null primary key, 
                TS timestamp, result text) }
            );

    #
    # collects the list of triggers to be created
    #
    my $triggers = safe_selectall_arrayref(
            $dbh,
            qq{select trigger_name, coalesce(trigger_body, '') as trigger_body
                from trigger_request where done = 0}, 
            {Slice => {}}
            );
    
    # 
    # collects the list of existing triggers, so we know which
    # ones we need to drop before creation
    # 

    my $existing_triggers_list = safe_selectcol_arrayref(
            $dbh,   
            qq{select trigger_name 
               from information_schema.triggers 
               where trigger_schema = ?}, 
            undef, $db);
    
    my %existing_triggers = map { $_, 1 } @$existing_triggers_list;
     
    # 
    # trigger creation loop
    # 
    TRIGGERS:
    for my $trig (@$triggers)
    {
        if ($DEBUG) {
            print "DB: $db\n", Data::Dumper->Dump([$trig],['trig']);
        }
        last TRIGGERS unless exists $trig->{trigger_name};
        last TRIGGERS unless exists $trig->{trigger_body};
        my $result = undef;
        #
        # removing trailing spaces from trigger definition
        #
        $trig->{trigger_body} =~ s/^\s+//x; 
        $trig->{trigger_body} =~ s/\s+$//x;
        #
        # sanitizing check. We are going to execute only queries that are
        # trigger creations, without any database specification
        # 
        if ( ( $trig->{trigger_body} eq q{} ) # = only drop trigger request
                                              # Notice that the query will convert
                                              # any NULL trigger body to ''
              or 
              ($trig->{trigger_body} =~ /^\s* create \s+ trigger \s+ $trig->{trigger_name}/xi) )
        {

            # 
            # more sanitizing checks
            # 
            
            if (my ($tdb,$ttable) = $trig->{trigger_body} =~ /(\w+|`[^`]+`)\.(\w+|`[^`]+`)/xi)
            {
                if ($tdb ne $db)
                {
                    set_result( 
                        $dbh, 
                        $trig->{trigger_name}, 
                        "REJECTED: Attempt at using database $tdb from database $db");
                    next TRIGGERS;
                }
            }
            
            # 
            # The requested trigger will be dropped first, if exists.
            # 
            if (exists $existing_triggers{$trig->{trigger_name}}) {
                eval {
                    $dbh->do(qq[drop trigger $trig->{trigger_name}] ) ;
                } ;
                if ($EVAL_ERROR) {
                    set_result( 
                        $dbh, 
                        $trig->{trigger_name}, 
                        $EVAL_ERROR);
                    next TRIGGERS;
                }    
                else {
                    $result = 'OK';
                }
            }
            if ( $trig->{trigger_body} ne q{} )  { # if the body is empty, skip the creation
                #
                # for future versions of MySQL. Starting 5.0.17 a DEFINER clause
                # can be used.
                # 
                $trig->{trigger_body} =~ s{^\s* create}{CREATE /*!50017 DEFINER=CURRENT_USER*/ }xi;
            
                #
                # This is the main point. The trigger is created here
                # 
                eval {
                    $dbh->do($trig->{trigger_body});
                };
                if ($EVAL_ERROR) {
                    $result = $EVAL_ERROR;
                }
                else {
                    $result = 'OK';
                }
            }
        }
        # 
        # if the initial check failed, we report that such query was 
        # not accepted
        # 
        else {
            $result = 'SQL command not recognized as a CREATE TRIGGER';
        }
        # 
        # finally, we report the results
        # to the trigger_answer table
        set_result( $dbh, $trig->{trigger_name}, $result);
    }
}

sub set_result {
    my ($dbh,$trigger_name, $result) = @_;
    $result =~ s/^ .* do failed: \s* //x;
    $result =~ s/at \s+ line \s+ \d+ \s+ at \s+ \S+ \s+ line \s+ \d+ \W*$//x;
    safe_do(
        $dbh,
        qq{insert into trigger_answer (trigger_name, result) values (?, ?) 
           on duplicate key update result = ?}, 
        undef, 
        $trigger_name, $result, $result);
   
    safe_do( 
        $dbh, 
        qq{update trigger_request set done = 1 where trigger_name = ?}, 
        undef , 
        $trigger_name);
    if ($DEBUG) {
        print "RESULT: $result\n";
    }
}

sub use_db {
    my ($dbh,$db) = @_;
    # prepared statements do not support "use database_name" 
    my $save_prepare_option = $dbh->{mysql_emulated_prepare};
    $dbh->{mysql_emulated_prepare}=1;
    eval { $dbh->do(qq{use $db}) };
    if ($EVAL_ERROR) {
        croak "unable to change to database $db\n";
    }
    $dbh->{mysql_emulated_prepare}= $save_prepare_option;
}
    
sub safe_selectcol_arrayref {
    my ($dbh, $query, $options, @params) = @_;
    my $result;
    # This should be the normal call
    # my $result = $dbh->selectcol_arrayref($query); 
    # 
    # the following ugly hack is a workaround for a bug in
    # DBD::mysql 3.0002_4 (http://bugs.mysql.com/bug.php?id=15546)
    #
    eval {
        if (@params) {
            $result = $dbh->selectall_arrayref($query, $options, @params);
        }
        else {
            $result = $dbh->selectall_arrayref($query, $options);
        }
    };
    if ($EVAL_ERROR) {
        croak "error executing query: $query\n$EVAL_ERROR\n";
    }
    return [ map {$_->[0] } @$result ];
}

sub safe_do {
    my ($dbh, $query, $options, @params) = @_;
    my $result;
    eval {
        if (@params) {
            $result = $dbh->do($query,$options,@params);
        }
        else {
            $result = $dbh->do($query,$options);
        }
        if ($EVAL_ERROR) {
            croak "error executing query: $query\n$EVAL_ERROR\n";
        }
    };
    return $result;
}

sub safe_selectall_arrayref {
    my ($dbh, $query, $options, @params) = @_;
    my $result;
    eval {
        if (@params) {
            $result = $dbh->selectall_arrayref($query,$options,@params);
        }
        else {
            $result = $dbh->selectall_arrayref($query,$options);
        }
        if ($EVAL_ERROR) {
            croak "error executing query: $query\n$EVAL_ERROR\n";
        }
    };
    return $result;
}
