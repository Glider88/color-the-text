const dropdownButton = document.getElementById('dropdownButton') as HTMLButtonElement | null
const dropdownMenu = document.getElementById('dropdownMenu') as HTMLElement | null
const currentMenu = document.getElementById('currentMenu') as HTMLElement | null

if (dropdownButton && dropdownMenu && currentMenu) {
    const menuItems = dropdownMenu.getElementsByTagName('button')
    Array.from(menuItems).forEach((menuItem: HTMLButtonElement) => {
        menuItem.addEventListener('click', function(e: Event) {
            e.stopPropagation()
            dropdownMenu.classList.toggle('show')
            currentMenu.textContent = menuItem.textContent || ''
        })
    })

    dropdownButton.addEventListener('click', function(e: Event) {
        e.stopPropagation()
        dropdownMenu.classList.toggle('show')
    })

    document.addEventListener('click', function(e: MouseEvent) {
        if (!dropdownButton.contains(e.target as Node) && !dropdownMenu.contains(e.target as Node)) {
            dropdownMenu.classList.remove('show')
        }
    })
}
