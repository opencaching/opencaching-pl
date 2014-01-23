function createcookie(nazwa,wartosc) {
     var dzis = new Date();
     dzis.setTime(dzis.getTime() + 1000*60*60*24*365);
     document.cookie = nazwa+"="+wartosc+";expires="+dzis;
}

function reloadpage(){
    window.location.reload();
}