document.addEventListener('DOMContentLoaded', () => {
    // Favorites Logic
    const favoriteBtns = document.querySelectorAll('.imob-favorite-btn');
    
    // Load favorites from local storage
    let favorites = JSON.parse(localStorage.getItem('imob_favorites')) || [];

    function updateBtnState(btn, id) {
        if (favorites.includes(id)) {
            btn.classList.add('is-favorite');
            btn.textContent = 'Favorito';
        } else {
            btn.classList.remove('is-favorite');
            btn.textContent = 'Favoritar';
        }
    }

    favoriteBtns.forEach(btn => {
        const id = btn.getAttribute('data-id');
        updateBtnState(btn, id);

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (favorites.includes(id)) {
                favorites = favorites.filter(favId => favId !== id);
            } else {
                favorites.push(id);
            }
            localStorage.setItem('imob_favorites', JSON.stringify(favorites));
            updateBtnState(btn, id);
        });
    });
});
