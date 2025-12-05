// phoenixData sudah didefinisikan di dashboard.php sebagai array objek:
// [{ id, name, image, price, url }, ...]

// Function untuk create phoenix card
function createPhoenixCard(data) {
    return `
        <div class="phoenix-card">
            <div class="card-image">
                <img src="${data.image}" alt="${data.name}">
                <button class="favorite-btn" onclick="toggleFavorite(this)">
                    <span class="heart">♥</span>
                </button>
            </div>
            <div class="card-footer">
                <h3 class="card-title">${data.name}</h3>
                <div class="price-tag">
                    <span class="coin-small">🪙</span>
                    <span class="price">${data.price}</span>
                </div>
                <a href="${data.url}" class="recruit-btn">Rekrut</a>
            </div>
        </div>
    `;
}

// Function untuk toggle favorite (hanya efek visual di front-end)
function toggleFavorite(button) {
    button.classList.toggle('active');
    const heart = button.querySelector('.heart');
    
    if (button.classList.contains('active')) {
        heart.style.color = '#ff0000';
        button.style.background = '#fff';
    } else {
        heart.style.color = '#ff6600';
        button.style.background = 'rgba(255, 255, 255, 0.9)';
    }
}

// Generate cards saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('phoenixGrid');
    
    if (grid && Array.isArray(phoenixData)) {
        phoenixData.forEach(data => {
            grid.innerHTML += createPhoenixCard(data);
        });
    }
});

// Optional: Smooth scroll untuk navigation (kalau href="#...")
document.querySelectorAll('.nav-menu a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
});
