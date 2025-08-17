<?php include 'includes/navbar.php'; ?>

<link rel="stylesheet" href="assets/css/blog.css">


<section class="breadcrumb-blog set-bg" data-setbg="/assets/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Our Blog</h2>
            </div>
        </div>
    </div>
</section>

<br>
<br>
<br>
<br>
<br>

<!-- Blog Section Begin -->
<section class="blog spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="/assets/blog/blog-1.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 16 February 2020</span>
                        <h5>What Curling Irons Are The Best Ones</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-2.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 21 February 2020</span>
                        <h5>The Eternity Bands Do Last Forever</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-3.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 28 February 2020</span>
                        <h5>The Health Benefits Of Sunglasses</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-4.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 16 February 2020</span>
                        <h5>Aiming For Higher The Mastopexy</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-5.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 21 February 2020</span>
                        <h5>Wedding Rings A Gift For A Lifetime</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-6.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 28 February 2020</span>
                        <h5>The Different Methods Of Hair Removal</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-7.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 16 February 2020</span>
                        <h5>Hoop Earrings A Style From History</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-8.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 21 February 2020</span>
                        <h5>Lasik Eye Surgery Are You Ready</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic set-bg" data-setbg="assets/blog/blog-9.jpg"></div>
                    <div class="blog__item__text">
                        <span><img src="/assets/icon/calendar.png" alt=""> 28 February 2020</span>
                        <h5>Lasik Eye Surgery Are You Ready</h5>
                        <a href="#">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Blog Section End -->


<script>
// Wait until the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function() {
    // Select all elements with the class 'set-bg'
    const elements = document.querySelectorAll(".set-bg");

    elements.forEach(function(el) {
        // Get the data-setbg attribute value
        const bg = el.getAttribute("data-setbg");
        if (bg) {
            // Set it as a background image
            el.style.backgroundImage = `url('${bg}')`;
        }
    });
});
</script>



<?php include 'includes/footer.php'; ?>