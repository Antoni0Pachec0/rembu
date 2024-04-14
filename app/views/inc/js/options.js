


function redirect() {
    var selectBox = document.getElementById("menu");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;

    if (selectedValue === "opcion1") {
        window.location.href = "pagina1.html";
    } else if (selectedValue === "opcion2") {
        window.location.href = "pagina2.html";
    } else if (selectedValue === "opcion3") {
        window.location.href = "pagina3.html";
    }
}

