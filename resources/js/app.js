import './bootstrap';
import './search';
import 'flowbite';
import ShoppingCart from './cart';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    try {
        window.cart = new ShoppingCart();
        console.log('Shopping cart Initialized');
    } catch (error) {
        console.error('Shopping cart not initialized', error);
    }
});


// document.addEventListener('DOMContentLoaded', function () {
//     const filterForm = document.getElementById('filter-form');
//     const productsContainer = document.getElementById('products-container');
//     const paginationLinks = document.getElementById('pagination-links');

//     // Handle form submission for filtering
//     filterForm.addEventListener('submit', function (e) {
//         e.preventDefault();
//         updateProducts(this.action, new FormData(this));
//     });

//     // Delegate click event for pagination links
//     document.body.addEventListener('click', function (e) {
//         if (e.target.closest('#pagination-links a')) {
//             e.preventDefault();
//             const url = e.target.closest('#pagination-links a').getAttribute('href');
//             updateProducts(url);
//         }
//     });

//     // Function to fetch and update products
//     function updateProducts(url, formData = null) {
//         const queryString = formData ? new URLSearchParams(formData).toString() : '';
//         const fetchUrl = formData ? `${url}?${queryString}` : url;

//         fetch(fetchUrl, {
//             method: 'GET',
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest',
//             },
//         })
//             .then(response => {
//                 if (!response.ok) throw new Error('Network response was not ok');
//                 return response.text();
//             })
//             .then(html => {
//                 productsContainer.innerHTML = html;
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//             });
//     }
// });



