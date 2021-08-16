window.onload = () => {
    const FiltersForm = document.querySelector("#filters");

    // On boucle sur les input
    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change", () => {

            const Form = new FormData(FiltersForm);

            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key, value);
            });

            const Url = new URL(window.location.href);

            // requête ajax
            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()
            ).then(data => {
                // chercher la zone de contenu
                const content = document.querySelector("#content");

                // remplace le contenu
                content.innerHTML = data.content;

                // Mise à jour de l'url
                history.pushState({}, null, Url.pathname + "?" + Params.toString());
            }).catch(e => alert(e));

        });
    });
}