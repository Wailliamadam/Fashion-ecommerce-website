document.addEventListener("DOMContentLoaded", function () {
  // FAQ item toggle
  const faqItems = document.querySelectorAll(".faq-item");

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");

    question.addEventListener("click", () => {
      item.classList.toggle("active");
    });
  });

  // Category filtering
  const categories = document.querySelectorAll(".category");
  const faqSections = document.querySelectorAll(".faq-section");

  categories.forEach((category) => {
    category.addEventListener("click", () => {
      // Update active category
      categories.forEach((c) => c.classList.remove("active"));
      category.classList.add("active");

      const selectedCategory = category.dataset.category;

      // Filter FAQ sections
      faqSections.forEach((section) => {
        if (
          selectedCategory === "all" ||
          section.dataset.category === selectedCategory
        ) {
          section.style.display = "block";
        } else {
          section.style.display = "none";
        }
      });
    });
  });

  // Search functionality
  const searchInput = document.querySelector(".search-input");
  const searchBtn = document.querySelector(".search-btn");

  function performSearch() {
    const searchTerm = searchInput.value.trim().toLowerCase();

    if (searchTerm === "") {
      faqItems.forEach((item) => {
        item.style.display = "flex";
      });
      return;
    }

    faqItems.forEach((item) => {
      const question = item
        .querySelector(".faq-question h4")
        .textContent.toLowerCase();
      const answer = item
        .querySelector(".faq-answer p")
        .textContent.toLowerCase();

      if (question.includes(searchTerm) || answer.includes(searchTerm)) {
        item.style.display = "flex";
        if (answer.includes(searchTerm)) {
          item.classList.add("active");
        }
      } else {
        item.style.display = "none";
      }
    });
  }

  searchBtn.addEventListener("click", performSearch);
  searchInput.addEventListener("keyup", function (e) {
    if (e.key === "Enter") {
      performSearch();
    }
  });
});
