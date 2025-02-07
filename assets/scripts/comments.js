$(document).ready(function() {
    $("form").on("submit", function(e) {
        e.preventDefault(); // Zatrzymuje przeładowanie strony

        tinyMCE.triggerSave(); // Zapisuje edytor do textarea

        let form = $(this);
        let formData = form.serialize(); // Pobiera dane formularza

        $.ajax({
            type: form.attr("method"), // Pobiera metodę np. POST
            url: form.attr("action"), // Pobiera adres formularza
            data: formData,
            success: function(response) {
                tinymce.get("mytextarea").setContent(""); // Czyści edytor
                form[0].reset(); // Czyści cały formularz
            },
            error: function() {
                alert("Błąd wysyłania komentarza.");
            }
        });
    });
});