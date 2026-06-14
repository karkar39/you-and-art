// Простой скрипт для плавного скролла и мелких интерактивов
document.addEventListener('DOMContentLoaded', function() {
    // Плавный скролл для якорей
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Обновление счетчика корзины (если нужно)
    function updateCartCountDisplay() {
        const cartCountSpan = document.getElementById('cartCount');
        if(cartCountSpan) {
            // Можно сделать AJAX запрос, но пока оставляем как есть
        }
    }
});