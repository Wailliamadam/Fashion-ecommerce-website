<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'db.php'; ?>
<!-- Navigation bar -->
<link rel="stylesheet" href="assets/css/style.css">
<!-- <link rel="stylesheet" href="assets/css/owl-nav.css"> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- Include in <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

<!-- Include before </body> -->
<script src="/assets/js/owl-nav.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>  -->




<!-- Hero Section -->

<!-- <div class="owl-carousel owl-theme"> -->
<section class="hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center">
            <!-- Text and Image -->
            <div class="col-md-6 text-center text-md-start">
                <p class="text-danger fw-semibold text-uppercase mb-2">Summer Collection</p>
                <h1 class="display-4 fw-bold">Fall - Winter Collections 2030</h1>
                <p class="lead text-muted">A specialist label creating luxury essentials. Ethically crafted with an
                    unwavering commitment to exceptional quality.</p>
                <a href="shop.php" class="btn btn-dark mt-3">Shop Now →</a>
                <br>
                <br>
                <br>
                <br>
                <br>
                <div class="hero__social mt-3">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-pinterest"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                </div>
            </div>
            <!-- <div class="col-md-6 text-center mt-4 mt-md-0">
                <img class="img-fluid hero-img" src="/assets/hero-1.jpg" alt="Hero Image">
            </div> -->
        </div>
    </div>
</section>

<!-- You can copy/paste this section for more slides -->
<!-- </div> -->



<!-- Include Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


<section class="banner spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 offset-lg-4">
                <div class="banner__item">
                    <div class="banner__item__pic">
                        <img src="/assets/banner/banner-1.jpg" alt="" />
                    </div>
                    <div class="banner__item__text">
                        <h2>Clothing Collections 2030</h2>
                        <a href="#">Shop now</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="banner__item banner__item--middle">
                    <div class="banner__item__pic">
                        <img src="/assets/banner/banner-2.jpg" alt="" />
                    </div>
                    <div class="banner__item__text">
                        <h2>Accessories</h2>
                        <a href="#">Shop now</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="banner__item banner__item--last">
                    <div class="banner__item__pic">
                        <img src="/assets/banner/banner-3.jpg" alt="" />
                    </div>
                    <div class="banner__item__text">
                        <h2>Shoes Spring 2030</h2>
                        <a href="#">Shop now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<br>
<br>
<br>
<br>
<br>

<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="filter__controls">
                    <li class="active" data-filter="*">Best Sellers</li>
                    <li data-filter=".new-arrivals">New Arrivals</li>
                    <li data-filter=".hot-sales">Hot Sales</li>
                </ul>
            </div>
        </div>
        <div class="row product__filter">
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix new-arrivals">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-1.jpg">
                        <span class="label">New</span>
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Zara Men's Sneakers</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 3,000,00</h5>
                        <div class="product__color__select">
                            <label for="pc-1">
                                <input type="radio" id="pc-1" />
                            </label>
                            <label class="active black" for="pc-2">
                                <input type="radio" id="pc-2" />
                            </label>
                            <label class="grey" for="pc-3">
                                <input type="radio" id="pc-3" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix hot-sales">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-2.jpg">
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Zara Men's Jacket with Pockets</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 4,000,00</h5>
                        <div class="product__color__select">
                            <label for="pc-4">
                                <input type="radio" id="pc-4" />
                            </label>
                            <label class="active black" for="pc-5">
                                <input type="radio" id="pc-5" />
                            </label>
                            <label class="grey" for="pc-6">
                                <input type="radio" id="pc-6" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix new-arrivals">
                <div class="product__item sale">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-3.jpg">
                        <span class="label">Sale</span>
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>HRX by Hrithik Roshan</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 2,500,00</h5>
                        <div class="product__color__select">
                            <label for="pc-7">
                                <input type="radio" id="pc-7" />
                            </label>
                            <label class="active black" for="pc-8">
                                <input type="radio" id="pc-8" />
                            </label>
                            <label class="grey" for="pc-9">
                                <input type="radio" id="pc-9" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix hot-sales">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-4.jpg">
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Zara Suede Pullover</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 3,500,00</h5>
                        <div class="product__color__select">
                            <label for="pc-10">
                                <input type="radio" id="pc-10" />
                            </label>
                            <label class="active black" for="pc-11">
                                <input type="radio" id="pc-11" />
                            </label>
                            <label class="grey" for="pc-12">
                                <input type="radio" id="pc-12" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix new-arrivals">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-5.jpg">
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Zara Chillida T-shirt</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 9,500,0</h5>
                        <div class="product__color__select">
                            <label for="pc-13">
                                <input type="radio" id="pc-13" />
                            </label>
                            <label class="active black" for="pc-14">
                                <input type="radio" id="pc-14" />
                            </label>
                            <label class="grey" for="pc-15">
                                <input type="radio" id="pc-15" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix hot-sales">
                <div class="product__item sale">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-6.jpg">
                        <span class="label">Sale</span>
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Lightweight Soft Fringe Scarf</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 4,500,00</h5>
                        <div class="product__color__select">
                            <label for="pc-16">
                                <input type="radio" id="pc-16" />
                            </label>
                            <label class="active black" for="pc-17">
                                <input type="radio" id="pc-17" />
                            </label>
                            <label class="grey" for="pc-18">
                                <input type="radio" id="pc-18" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix new-arrivals">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-7.jpg">
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Zara Leather Backpack</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 5,500,00</h5>
                        <div class="product__color__select">
                            <label for="pc-19">
                                <input type="radio" id="pc-19" />
                            </label>
                            <label class="active black" for="pc-20">
                                <input type="radio" id="pc-20" />
                            </label>
                            <label class="grey" for="pc-21">
                                <input type="radio" id="pc-21" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-md-6 col-sm-6 mix hot-sales">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="assets/products/product-8.jpg">
                        <ul class="product__hover">
                            <li>
                                <a href="#"><img src="/assets/icon/heart.png" alt="" /></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/compare.png" alt="" />
                                    <span>Compare</span></a>
                            </li>
                            <li>
                                <a href="#"><img src="/assets/icon/search.png" alt="" /></a>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6>Collarless Polo</h6>
                        <a href="#" class="add-cart">+ Add To Cart</a>
                        <div class="rating">
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <h5>MMK - 2,000,00</h5>
                        <div class="product__color__select">
                            <label for="pc-22">
                                <input type="radio" id="pc-22" />
                            </label>
                            <label class="active black" for="pc-23">
                                <input type="radio" id="pc-23" />
                            </label>
                            <label class="grey" for="pc-24">
                                <input type="radio" id="pc-24" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
document.querySelectorAll('.set-bg').forEach(function(el) {
    const bg = el.getAttribute('data-setbg');
    if (bg) el.style.backgroundImage = `url(${bg})`;
});


const filterControls = document.querySelectorAll('.filter__controls li');
const productItems = document.querySelectorAll('.product__filter .mix');


filterControls.forEach(function(control) {
    control.addEventListener('click', function() {
        // Remove active from all
        filterControls.forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        const filterValue = this.getAttribute('data-filter');

        productItems.forEach(function(item) {
            if (filterValue === '*' || item.classList.contains(filterValue.slice(1))) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<section class="categories spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="categories__text">
                    <h2>
                        Clothings Hot <br />
                        <span>Shoe Collection</span> <br />
                        Accessories
                    </h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="categories__hot__deal">
                    <img src="/assets/product-sale.png" alt="" />
                    <div class="hot__deal__sticker">
                        <span>Sale Of</span>
                        <h5>MMK - 1,000,00</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 offset-lg-1">
                <div class="categories__deal__countdown">
                    <span>Deal Of The Week</span>
                    <h2>Multi-pocket Chest Bag Black</h2>
                    <div class="categories__deal__countdown__timer" id="countdown">
                        <div class="cd-item">
                            <span>3</span>
                            <p>Days</p>
                        </div>
                        <div class="cd-item">
                            <span>1</span>
                            <p>Hours</p>
                        </div>
                        <div class="cd-item">
                            <span>50</span>
                            <p>Minutes</p>
                        </div>
                        <div class="cd-item">
                            <span>18</span>
                            <p>Seconds</p>
                        </div>
                    </div>
                    <a href="#" class="primary-btn">Shop now</a>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
function startCountdown() {
    const countdownElement = document.getElementById('countdown');
    const endTime = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000); // 3 days from now

    function updateCountdown() {
        const now = new Date();
        const timeLeft = endTime - now;

        if (timeLeft <= 0) {
            countdownElement.innerHTML =
                '<span>00</span><p>Days</p><span>00</span><p>Hours</p><span>00</span><p>Minutes</p><span>00</span><p>Seconds</p>';
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        countdownElement.innerHTML = `
                <div class="cd-item"><span>${days}</span><p>Days</p></div>
                <div class="cd-item"><span>${hours}</span><p>Hours</p></div>
                <div class="cd-item"><span>${minutes}</span><p>Minutes</p></div>
                <div class="cd-item"><span>${seconds}</span><p>Seconds</p></div>
            `;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
}
document.addEventListener('DOMContentLoaded', startCountdown);
</script>






<div class="container instagram-section">
    <div class="row">
        <!-- Left side image grid (6 images) -->
        <div class="col-lg-8">
            <div class="row g-2">
                <div class="col-4">
                    <img src="assets/instagram/instagram-1.jpg" alt="Image 1" class="instagram-img">
                </div>
                <div class="col-4">
                    <img src="assets/instagram/instagram-2.jpg" alt="Image 2" class="instagram-img">
                </div>
                <div class="col-4">
                    <img src="assets/instagram/instagram-3.jpg" alt="Image 3" class="instagram-img">
                </div>
                <div class="col-4">
                    <img src="assets/instagram/instagram-4.jpg" alt="Image 4" class="instagram-img">
                </div>
                <div class="col-4">
                    <img src="assets/instagram/instagram-5.jpg" alt="Image 5" class="instagram-img">
                </div>
                <div class="col-4">
                    <img src="assets/instagram/instagram-6.jpg" alt="Image 6" class="instagram-img">
                </div>
            </div>
        </div>

        <!-- Right side text -->
        <div class="col-lg-4 d-flex flex-column justify-content-center instagram-text">
            <h2 class="fw-bold mb-3">Instagram</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua.</p>
            <div class="hashtag">#Male_Fashion</div>
        </div>
    </div>
</div>


<section class="latest spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span>Latest News</span>
                    <h2>Fashion New Trends</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="/assets/blog/blog-1.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt="" /> 16 February
                            2026</span>
                        <h5>What Curling Irons Are The Best Ones</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="/assets/blog/blog-2.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt="" /> 21 February
                            2026</span>
                        <h5>The Eternity Bands Do Last Forever</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="/assets/blog/blog-3.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt="" /> 28 February
                            2026</span>
                        <h5>The Health Benefits Of Sunglasses</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
document.querySelectorAll(".set-bg").forEach(function(element) {
    const bg = element.getAttribute("data-setbg");
    if (bg) {
        element.style.backgroundImage = `url(${bg})`;
    }
});
</script>



<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons (optional for calendar icon) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- Bootstrap 5 JS (Required for tabs) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: JavaScript for smoother transitions -->
<script>
// Enhance Bootstrap's default tab behavior
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#productTabs button[data-bs-toggle="tab"]');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetId = tab.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);

            // Force reflow to restart animation
            targetPane.style.display = 'none';
            targetPane.offsetHeight; // Trigger reflow
            targetPane.style.display = 'block';
        });
    });
});
</script>


<?php include 'includes/footer.php'; ?>