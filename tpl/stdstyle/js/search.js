/*
 * *********************************************
 *  Search reasult caches - JG (triPPer)
 *
 *                              author: tripper1971@wp.pl
 *
*************************************************
 *
*/





function EventSelectPosFunction( event )
{
    var RecArray =  gct.getSelection();
    var nrSel = RecArray.length;

    document.ExportCaches.SelectedPos.value = nrSel;
}

function EventPageFunction(event)
{
    document.getElementById("pageNumber").innerHTML = event['page']+1;
}

function CacheExport( type )
{
    var link = 'search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=';

    if( type == 'ggzp')
        link = link + 'zip&format=ggz';
    else
        link = link + type;

    var item;
    var CacheID = [];
    var RecArray =  gct.getSelection();


    for (var i = 0; i < RecArray.length; i++)
    {
       item = RecArray[i];

       val = gct.getValue( item.row, 0 );
       CacheID[ i ] = val;
    }

    if ( RecArray.length )
    {
        link = link+"&SearchCacheID="+CacheID;
        window.location.href = link;
    }
    else
    {
        alert( 'Nie zaznaczono żadnej pozycji' );
    }
}
