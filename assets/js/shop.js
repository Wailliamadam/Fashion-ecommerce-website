// Filter functions
function filterPrice(min, max) {
  window.location.href = `products.php?price_min=${min}&price_max=${max}`;
}

function filterSize(size) {
  window.location.href = `products.php?size=${size}`;
}

function filterTag(tag) {
  window.location.href = `products.php?tag=${tag}`;
}

// Highlight active filters
document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);

  // Highlight active category
  const activeCategory = urlParams.get("category");
  if (activeCategory) {
    document.querySelectorAll(".category-list a").forEach((link) => {
      if (link.textContent.includes(activeCategory)) {
        link.style.color = "var(--accent)";
        link.style.fontWeight = "bold";
      }
    });
  }

  // Highlight active brand
  const activeBrand = urlParams.get("brand");
  if (activeBrand) {
    document.querySelectorAll(".brand-list a").forEach((link) => {
      if (link.textContent.includes(activeBrand)) {
        link.style.color = "var(--accent)";
        link.style.fontWeight = "bold";
      }
    });
  }

  // Highlight active size
  const activeSize = urlParams.get("size");
  if (activeSize) {
    document.querySelectorAll(".size-option").forEach((option) => {
      if (option.textContent.trim() === activeSize) {
        option.classList.add("active");
      }
    });
  }

  // Highlight active tag
  const activeTag = urlParams.get("tag");
  if (activeTag) {
    document.querySelectorAll(".tag-option").forEach((option) => {
      if (option.textContent.trim() === activeTag) {
        option.classList.add("active");
      }
    });
  }

  // Highlight active price range
  const priceMin = urlParams.get("price_min");
  const priceMax = urlParams.get("price_max");
  if (priceMin && priceMax) {
    const priceInputs = document.querySelectorAll(".price-option input");
    priceInputs.forEach((input) => {
      const range = input.nextElementSibling.textContent;
      const [min, max] = range.replace("$", "").split(" - ").map(Number);
      if (min == priceMin && max == priceMax) {
        input.checked = true;
      }
    });
  }
});

/*------------------
    Accordion
--------------------*/
// This script will explicitly manage the collapse behavior for the accordion sections
$(document).ready(function () {
  // Select all accordion heading links
  $(".shop__sidebar__accordion .card-heading a").each(function () {
    var $this = $(this);
    var $target = $($this.attr("data-target"));

    // Check if the target collapse div has the 'show' class
    if ($target.hasClass("show")) {
      // If it's open, add the `aria-expanded` and remove the `collapsed` class
      $this.attr("aria-expanded", "true");
      $this.removeClass("collapsed");
    } else {
      // If it's closed, set `aria-expanded` to false and add the `collapsed` class
      $this.attr("aria-expanded", "false");
      $this.addClass("collapsed");
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Enable Add to Cart button when size is selected
  document.querySelectorAll(".product-size").forEach((select) => {
    select.addEventListener("change", function () {
      const productId = this.dataset.id;
      const addToCartBtn = document.querySelector(
        `.add-to-cart[data-id="${productId}"]`
      );
      addToCartBtn.disabled = !this.value;
    });
  });

  // Wishlist Functionality
  document.querySelectorAll(".add-to-wishlist").forEach((btn) => {
    btn.addEventListener("click", async function () {
      const productId = this.dataset.id;
      const productName = this.dataset.name;
      const size = document.querySelector(
        `.product-size[data-id="${productId}"]`
      ).value;
      const colorElement = document.querySelector(
        `.product-color[name="color_${productId}"]:checked`
      );
      const color = colorElement ? colorElement.value : null;

      // Show loading state
      const originalHTML = this.innerHTML;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      this.disabled = true;

      try {
        // Simulate API call - replace with actual fetch
        // const response = await fetch('api/add_to_wishlist.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ product_id: productId, size, color })
        // });
        // const data = await response.json();

        // For demo purposes - simulate success
        const data = {
          success: true,
          message: `${productName} added to wishlist!`,
        };

        showToast(data.message, "success");
        this.innerHTML = '<i class="fas fa-heart text-danger"></i> In Wishlist';

        // Update wishlist count in navbar if you have one
        const wishlistCount = document.querySelector(".wishlist-count");
        if (wishlistCount) {
          const currentCount = parseInt(wishlistCount.textContent) || 0;
          wishlistCount.textContent = currentCount + 1;
        }
      } catch (error) {
        showToast("Failed to add to wishlist", "error");
        this.innerHTML = originalHTML;
      } finally {
        this.disabled = false;
      }
    });
  });

  // Add to Cart Functionality
  document.querySelectorAll(".add-to-cart").forEach((btn) => {
    btn.addEventListener("click", async function () {
      const productId = this.dataset.id;
      const productName = this.dataset.name;
      const size = document.querySelector(
        `.product-size[data-id="${productId}"]`
      ).value;
      const colorElement = document.querySelector(
        `.product-color[name="color_${productId}"]:checked`
      );
      const color = colorElement ? colorElement.value : null;

      // Show loading state
      const originalHTML = this.innerHTML;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      this.disabled = true;

      try {
        // Simulate API call - replace with actual fetch
        // const response = await fetch('api/add_to_cart.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ product_id: productId, size, color, quantity: 1 })
        // });
        // const data = await response.json();

        // For demo purposes - simulate success
        const data = {
          success: true,
          message: `${productName} added to cart!`,
        };

        showToast(data.message, "success");

        // Update cart count in navbar if you have one
        const cartCount = document.querySelector(".cart-count");
        if (cartCount) {
          const currentCount = parseInt(cartCount.textContent) || 0;
          cartCount.textContent = currentCount + 1;
        }
      } catch (error) {
        showToast("Failed to add to cart", "error");
      } finally {
        this.innerHTML = originalHTML;
        this.disabled = false;
      }
    });
  });

  // Toast Notification Function
  function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
            <div class="toast-body">
                <i class="fas ${
                  type === "success"
                    ? "fa-check-circle"
                    : "fa-exclamation-circle"
                } me-2"></i>
                ${message}
            </div>
        `;

    // Add styles
    toast.style.cssText = `
            background: ${type === "success" ? "#28a745" : "#dc3545"};
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
            display: flex;
            align-items: center;
        `;

    document.getElementById("toast-container").appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
      toast.style.animation = "fadeOut 0.3s ease-out";
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Add CSS animations
  const style = document.createElement("style");
  style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
  document.head.appendChild(style);
});
