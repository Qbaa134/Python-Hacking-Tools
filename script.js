function searchAirline() {
    const query = document.getElementById("searchInput").value;
    const resultsDiv = document.getElementById("results");
    resultsDiv.innerHTML = "";

    // Fetch data from Wikipedia API
    fetch(`https://en.wikipedia.org/w/api.php?action=query&format=json&origin=*&prop=extracts|pageimages&titles=${query}&exintro=1&piprop=original`)
        .then(response => response.json())
        .then(data => {
            const pages = data.query.pages;
            for (const pageId in pages) {
                const page = pages[pageId];
                const title = page.title;
                const extract = page.extract;
                const imageUrl = page.original ? page.original.source : 'https://via.placeholder.com/300';

                // Fetch airplane image from Unsplash
                fetch(`https://source.unsplash.com/600x400/?${title} airplane`)
                    .then(imgResponse => {
                        const airlineCard = `
                            <div class="airline-card">
                                <img src="${imgResponse.url}" alt="${title}">
                                <h3>${title}</h3>
                                <p>${extract}</p>
                            </div>
                        `;
                        resultsDiv.innerHTML += airlineCard;
                    });
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = "<p>No information found. Please try a different search.</p>";
        });
}
