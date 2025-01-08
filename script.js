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

                const airlineCard = `
                    <div class="airline-card">
                        <h3>${title}</h3>
                        <img src="${imageUrl}" alt="${title}">
                        <p>${extract}</p>
                    </div>
                `;
                resultsDiv.innerHTML += airlineCard;
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = "<p>No information found. Please try a different search.</p>";
        });
}
