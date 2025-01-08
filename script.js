function searchAirline() {
    const query = document.getElementById("searchInput").value.trim();
    const resultsDiv = document.getElementById("results");
    resultsDiv.innerHTML = "";

    if (!query) {
        resultsDiv.innerHTML = "<p>Please enter an airline name.</p>";
        return;
    }

    // Pobieranie danych z Wikipedii
    fetch(`https://en.wikipedia.org/w/api.php?action=query&format=json&origin=*&prop=extracts|pageimages&titles=${query}&exintro=1&piprop=thumbnail&pithumbsize=300`)
        .then(response => response.json())
        .then(data => {
            const pages = data.query.pages;
            let found = false;

            for (const pageId in pages) {
                const page = pages[pageId];
                if (page.missing) continue;
                found = true;

                const title = page.title;
                const extract = page.extract || "No information available.";
                const imageUrl = page.thumbnail ? page.thumbnail.source : 'https://via.placeholder.com/300';

                // Tworzenie karty linii lotniczej
                const airlineCard = `
                    <div class="airline-card">
                        <img src="${imageUrl}" alt="${title}">
                        <h3>${title}</h3>
                        <p>${extract}</p>
                    </div>
                `;
                resultsDiv.innerHTML += airlineCard;
            }

            if (!found) {
                resultsDiv.innerHTML = "<p>No information found. Please try a different airline.</p>";
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = "<p>Error fetching information. Please try again later.</p>";
        });

    // Pobieranie zdjęć samolotów z Unsplash
    fetch(`https://source.unsplash.com/600x400/?${query}-airplane`)
        .then(imgResponse => {
            const imgCard = `
                <div class="airline-card">
                    <img src="${imgResponse.url}" alt="${query} airplane">
                    <h3>${query}</h3>
                    <p>Photo of ${query} airplane.</p>
                </div>
            `;
            resultsDiv.innerHTML += imgCard;
        });
}
