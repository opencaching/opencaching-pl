<?php
use src\Utils\Database\XDb;
use src\Models\Coordinates\NutsLocation;
use src\Models\OcConfig\OcConfig;
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>

<?php
global $debug_page;
if ($debug_page)
    echo "<script>TimeTrack( 'DEBUG' );</script>";
?>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
    <tr>
        <td><?php
            echo '<center><table width="97%"><tr><td align="center"><center><b>{{Stats_s3a_01}}<br/></b>';
            echo '<br>({{Stats_s3a_02}})</center></td></tr></table><br><table border="1" bgcolor="white" width="30%">' . "\n";

            echo '<tr class="bgcolor2">
                    <th align="right">{{Stats_s3a_03}}:</b></th>
                  </tr><tr><td height="2"></td></tr>';

            foreach (NutsLocation::getRegionsListByCountryCodes(OcConfig::getSitePrimaryCountriesList()) as $record) {

                echo '<tr class="bgcolor2">
                        <td align="right">
                            <a class="links" href=articles.php?page=s11&region=' . $record['code'] . '>' . $record['name'] . '</a>
                        </td>';
            }

            echo '</table>' . "\n";

            XDb::xFreeResults($rs);
            ?>
        </td></tr>
</table>
</div>
