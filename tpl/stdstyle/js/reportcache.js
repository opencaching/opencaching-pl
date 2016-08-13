function hiddeCheck() {
    document.getElementById("statement").style.visibility = "hidden";
    document.getElementById("statement_label").style.visibility = "hidden";
    document.getElementById("sender").disabled = false;
}

function showCheck() {
    document.getElementById("statement").style.visibility = "visible";
    document.getElementById("statement_label").style.visibility = "visible";
    document.getElementById("sender").disabled = true;
    document.getElementById("sender").checked = false;
}

function statementChange() {
    document.getElementById("sender").disabled = !document.getElementById("statement").checked;
}
