
window.addEventListener("scroll", () => {
    const nav = document.getElementById("dashboard-nav");
    if (window.scrollY > 10) {
        nav.classList.add("scrolled");
    } else {
        nav.classList.remove("scrolled");
    }
});
