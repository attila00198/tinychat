cookieStore.getAll().then(data => {
    data.forEach(e => {
        console.table(e)
    });
})

let current_path = window.location.pathname
let links = document.getElementsByTagName("a")

for (const link of links) {
    if (current_path == link.pathname) {
        console.log(link.pathname)
        link.classList.toggle("active")
    }
}