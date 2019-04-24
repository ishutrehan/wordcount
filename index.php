<?php  require_once('common_function.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <style type="text/css">
    .nav-logo-g{
      display:inline-block;
      -webkit-transition:all 0.2s;
      transition:all 0.2s
    }
    .nav-logo:hover{
      color: #fff !important;
    }
    .nav-logo:hover .nav-logo-g
    {
      -webkit-transform:rotateY(180deg);
      -moz-transform:rotateY(180deg);
      -o-transform:rotateY(180deg);
      -ms-transform:rotateY(180deg);
      transform:rotateY(180deg);
    }
  </style>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="Bootstrap, Landing page, Template, Registration, Landing">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="author" content="Grayrids">
    <title>Website Wordcount</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
   <!--  <link rel="stylesheet" href="css/line-icons.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/nivo-lightbox.css"> -->
    <link rel="stylesheet" href="css/main.css">    
   <link rel="stylesheet" type="text/css" href="assets/jquery.confirm/jquery.confirm.css" />
    <link rel="stylesheet" href="css/responsive.css">

  </head>
  
  <body>
  <main>
      
        <!-- Header Section Start -->
        <header id="home" class="hero-area-2">    
          <nav class="navbar navbar-expand-md bg-inverse scrolling-navbar">
            <div class="container">
              <a href="index.php" class="navbar-brand nav-logo">
              LO<span class="nav-logo-g ">G</span>O</a>  
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="lni-menu"></i>
              </button>
            </div>
          </nav>  
            <div class="container-fluid">  
                 <!-- Subcribe Section Start -->
                <div id="subscribe" class="section">
                    <div class="row justify-content-center">
                      <div class="col-lg-9 col-md-12 col-xs-12">
                        <div class="subscribe-form">
                          <div class="form-wrapper">
                            <div class="sub-title text-center">
                              <h3>Get your website word count</h3>
                            </div>
                            <form method="post" class="submit_url">
                              <div class="row">
                                <div class="col-12 form-line">
                                  <div class="form-group form-search">
                                    <input type="text" class="form-control" name="url" placeholder="Enter URL here...">
                                  </div> 
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="result_section">
                        <div class="row">                        
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="col-md-3" style="float: left;">
                                    <p class="result_sub_heading">TOTAL WORD COUNT</p>
                                  <!--   <div class="ajax_loader"><img src="img/giphy.gif"></div> -->
                                 <div class="ajax_loader"></div>
                                    <div class="count_response"></div>
                                    <div class="text_information">
                                      <p>&check; Getting to all public URLs</p>
                                      <p>&check; Counting words in all your HTML</p>
                                      <p>&check; Success</p>
                                    </div>
                                </div>
                                <div class="col-md-9" style="float: left;">
                                    <p class="result_sub_heading">BY PAGE</p>
                                   <!--  <div class="ajax_loader"><img src="img/giphy.gif"></div> -->
                                  <div class="ajax_loader"></div>
                                    <div class="links_response"></div>
                                </div>
                            </div>
                             <div class="col-md-1"></div>
                        </div>
                    </div>
                </div>
            </div>         
        </header>
        <footer id="footer" class="footer" style="margin-top: 0px;">
        <div class="container text-wa">
            <a href="index.php">LOGO</a>
        </div>
        </footer>
  </main>

    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="js/jquery-min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- <script src="js/owl.carousel.js"></script> 
    <script src="js/jquery.mixitup.js"></script>       
    <script src="js/jquery.nav.js"></script>    
    <script src="js/scrolling-nav.js"></script>    
    <script src="js/jquery.easing.min.js"></script>     
    <script src="js/wow.js"></script>   
    <script src="js/jquery.counterup.min.js"></script>     
    <script src="js/nivo-lightbox.js"></script>     
    <script src="js/jquery.magnific-popup.min.js"></script>     
    <script src="js/waypoints.min.js"></script>       -->
    <!-- <script src="js/form-validator.min.js"></script>
    <script src="js/contact-form-script.js"></script>  -->  
    <script src="js/main.js"></script>
  <script src="assets/jquery.confirm/jquery.confirm.js"></script>
  <script src="https://wordcount.weglot.com/js/routing?callback=fos.Router.setData"></script>
   <!-- <script src="assets/blockBlock/adscript.js"></script>
    <script src="assets/blockBlock/blockBlock.jquery.js"></script>
    <script src="assets/js/script.js"></script>
 -->

    
  </body>

</html>