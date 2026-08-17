function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");

    const mainContent = document.getElementById("mainContent");

    const openBtn = document.getElementById("openBtn");


    sidebar.classList.toggle("closed");

    mainContent.classList.toggle("full");


    if (sidebar.classList.contains("closed")) {

        openBtn.style.display = "flex";

    } else {

        openBtn.style.display = "none";

    }

}