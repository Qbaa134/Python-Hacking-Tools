const apiKey = '23592b9a165ab83792cffa1bec6a95a0'; // Wpisz swój klucz API tutaj
const apiUrl = `https://aviationstack.com/api/v1/airlines?access_key=${apiKey}`;

async function searchAirline() {
    const query = document.getElementById("searchInput").value.toLowerCase();
    const resultsDiv = document.getElementById("results");
    resultsDiv.innerHTML = "Loading...";

    try {
        const response = await fetch(apiUrl);
        const data = await response.json();

        resultsDiv.innerHTML = "";

        const filteredAirlines = data.data.filter(airline =>
            airline.airline_name.toLowerCase().includes(query)
        );

        filteredAirlines.forEach(airline => {
            const airlineCard = `
                <div class="airline-card">
                    <h3>${airline.airline_name}</h3>
                    <p>Country: ${airline.country_name}</p>
                    <p>Founded: ${airline.founded}</p>
                    <p>IATA Code: ${airline.iata_code}</p>
                </div>
            `;
            resultsDiv.innerHTML += airlineCard;
        });

        if (filteredAirlines.length === 0) {
            resultsDiv.innerHTML = "No airlines found.";
        }
    } catch (error) {
        resultsDiv.innerHTML = "Error fetching data.";
        console.error(error);
    }
}
