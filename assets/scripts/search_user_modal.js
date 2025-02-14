document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("user-search");
    const resultsContainer = document.getElementById("search-results");
    const saveUserBtn = document.querySelector("#addUsersModal .btn-primary"); 
    const saveEditBtn = document.querySelector("#contributorSave"); 
    const projectId = document.getElementById("project-id")?.value; 

    
    function handleUserSave() {
        const email = searchInput.value;

        if (!email) {
            function handleUserSave() {
                const email = searchInput.value.trim();
        
                if (!email) {
                    alert("Wybierz użytkownika przed zapisaniem!");
                    return;
                }
        
                fetch("/add-user-to-project", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        email: email,
                        projectId: projectId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    searchInput.value = "";
                    resultsContainer.innerHTML = "";
                    resultsContainer.style.display = "none";
                })
                .catch(error => console.error("Błąd dodawania użytkownika:", error));
            }
        
            searchInput.addEventListener("input", handleSearch);
        
            document.addEventListener("click", function(event) {
                if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
                    resultsContainer.style.display = "none";
                }
            });
                 alert("Wybierz użytkownika przed zapisaniem!");
            return;
        }
        
        fetch("/add-user-to-project", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                email: email,
                projectId: projectId
            }) 
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            searchInput.value = "";
            resultsContainer.innerHTML = "";
            resultsContainer.style.display = "none";
            window.location.reload();
        })
        .catch(error => console.error("Błąd dodawania użytkownika:", error));
    };

    searchInput.addEventListener("input", function() {
        let query = this.value.trim();  

        if (query.length < 2) {
            resultsContainer.innerHTML = "";
            resultsContainer.style.display = "none";
            return;
        }

        fetch(`/search-user?q=${query}`)
            .then(response => response.json())
            .then(users => {
                resultsContainer.innerHTML = "";
                resultsContainer.style.display = "block";

                users.forEach(user => { 
                    let div = document.createElement("div");
                    div.classList.add("search-result-item");
                    div.innerHTML = `${user.email}`;
                    div.addEventListener("click", function(event) {
                        event.stopPropagation(); 
                        searchInput.value = user.email;
                        resultsContainer.innerHTML = "";
                        resultsContainer.style.display = "none";
                    });
                    resultsContainer.appendChild(div);
                });
            })
            .catch(error => console.error("Error fetching users:", error));
    });

    document.addEventListener("click", function(event) {
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.style.display = "none";
        }
    });

    if (saveUserBtn) saveUserBtn.addEventListener("click", handleUserSave);
    if (saveEditBtn) saveEditBtn.addEventListener("click", handleUserSave);

});
