
localStorage.setItem("user_id", "<?php echo $_SESSION['user_id']; ?>");
document.addEventListener("DOMContentLoaded", () => {
  // Example: Define products array (Replace this with your actual data source)
  let products = [
    {
      name: "Modern Sofa",
      price: "Rs 4,999",
    },
    {
      name: "Leather ArmChair",
      price: "Rs 20,000",
    },
    {
      name: "Modern Coffee Table",
      price: "Rs 1,500",
    },
    {
      name: "Wooden Dining Table",
      price: "Rs 9,999",
    },
    {
      name: "Wardrobe",
      price: "Rs 16,999",
    },
    {
      name: "TV Unit",
      price: "Rs 12,999",
    }
  ];

  // Get product container
  const productContainer = document.getElementById("products");

  // Ensure productContainer exists
  if (!productContainer) {
    console.error("Error: 'products' container not found in the DOM");
    return;
  }

  // Ensure products is an array before using forEach
  if (Array.isArray(products)) {
    products.forEach((product) => {
      const productCard = `
        <div class="product-card">
          <img src="${product.image}" alt="${product.name}">
          <h3>${product.name}</h3>
          <p>${product.price}</p>
          <button>Add to Cart</button>
        </div>`;
      productContainer.innerHTML += productCard;
    });
  } else {
    console.error("Error: 'products' is not an array", products);
  }
});


const productGrid = document.querySelector('.product-grid');

function scrollLeft() {
    productGrid.scrollBy({
        top: 0,
        left : -200, // Adjust the value to change the scroll distance
        behavior: 'smooth'
    });
}

function scrollRight() {
    productGrid.scrollBy({
        top: 0,
        left: 200, // Adjust the value to change the scroll distance
        behavior: 'smooth'
    });

}

function toggleSidebar() {
  let sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("active");
}

// Run script after DOM loads
document.addEventListener("DOMContentLoaded", () => {
  const hamburgerMenu = document.querySelector(".hamburger-menu");
  const closeBtn = document.querySelector(".close-btn");

  if (hamburgerMenu) {
      hamburgerMenu.addEventListener("click", toggleSidebar);
  }

  if (closeBtn) {
      closeBtn.addEventListener("click", toggleSidebar);
  }
});

document.getElementById('registration-form').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('/submit_registration', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        if (response.ok) {
            const result = await response.text();
            alert(result); // Show success message
            // Optionally redirect to another page
            // window.location.href = '/login';
        } else {
            const error = await response.text();
            alert('Error: ' + error); // Show error message
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An unexpected error occurred. Please try again later.');
    }
});

// Add event listeners to all "Add to Cart" buttons
document.querySelectorAll(".bestseller-add-to-cart").forEach(button => {
  button.addEventListener("click", function() {
      const productId = this.getAttribute("data-product-id");
      const productName = this.getAttribute("data-product-name");
      const productPrice = parseFloat(this.getAttribute("data-product-price"));
      const productImage = this.parentElement.parentElement.querySelector("img").src; // Get product image URL

      // Load existing cart
      let cart = JSON.parse(localStorage.getItem('cart')) || [];

      // Check if the product is already in the cart
      const existingProductIndex = cart.findIndex(item => item.id === productId );

      if (existingProductIndex > -1) {
          // If the product exists, increase the quantity
          cart[existingProductIndex].quantity += 1;
      } else {
          // If the product does not exist, add it with a quantity of 1
          cart.push({ id: productId, name: productName, price: productPrice, image: productImage, quantity: 1 });
      }

      // Save updated cart to localStorage
      localStorage.setItem('cart', JSON.stringify(cart));

      // Show pop-up message
      alert(`${productName} has been added to your cart!`);
  });
});