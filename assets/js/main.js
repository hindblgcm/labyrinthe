document.addEventListener("keydown", function(event) {
    // Vérifier les touches directionnelles
    if (event.key === "ArrowUp") {
        document.querySelector('button[name="direction"][value="up"]').click();
    } else if (event.key === "ArrowDown") {
        document.querySelector('button[name="direction"][value="down"]').click();
    } else if (event.key === "ArrowLeft") {
        document.querySelector('button[name="direction"][value="left"]').click();
    } else if (event.key === "ArrowRight") {
        document.querySelector('button[name="direction"][value="right"]').click();
    }
});