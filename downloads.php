<!DOCTYPE html>
<html lang="en">
  <head>
    <title>masterdesky</title>
    <meta
      name="description"           
      content="Downloadable content, I don't really know what
               will they be... Physics lecture notes for uni?
               Minecraft maps with CoreProtect files? Maybe goverment secrets?
               Who knows...">
    <!-- Viewport width is slightly bigger, than the page wrapper -->
    <meta 
      name="viewport"
      content="width=910, initial-scale=1">
    <!-- Add FA icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Add base css to page -->
    <link rel="stylesheet" type="text/css" href="/css/page_css.css">
    <!-- Add css for social icons -->
    <link rel="stylesheet" type="text/css" href="/css/icon_css.css">

    <link rel="icon" href="/assets/images/icons/icon.svg">
    <link rel="mask-icon" href="/assets/images/icons/mask-icon.svg" color="#000000">
    <link rel="apple-touch-icon" href="/assets/images/icons/apple-touch-icon.png">

    <!-- Adding MathJax -->
    <script type="text/x-mathjax-config">
      MathJax.Hub.Config({
        extensions: ["tex2jax.js", "TeX/AMSmath.js", "TeX/AMSsymbols.js"],
        jax: ["input/TeX", "output/SVG"],
        tex2jax: {
          inlineMath: [ ['$','$'], ["\\(","\\)"] ],
          displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
          processEscapes: true
        },
        menuSettings: {
          zoom: "Click"
        },
        SVG: {
          linebreaks: { automatic:true },
          font: "Latin-Modern"
        },
        MathZoom: {
          styles: {
            "#MathJax_Zoom": {
              "background-color": "#040A13"
            }
          }
        }
      });
    </script>
    <script type="text/javascript" async
      src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/latest.js?config=default">
    </script>
  </head>

  <body>
    <!-- transparent wrapper rectangle -->
    <div id="page-wrapper">
      <!-- clickable header image -->
      <div id="header-image-container">
        <a href="/">
          <img class="header-image" src="/assets/images/header/header.svg" alt="masterdesky">
        </a>
      </div>

      <!-- menu -->
      <div class="menu">
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="downloads.html">Downloads</a></li>
          <li><a href="projects.html">Projects</a></li>
        </ul>
      </div>

      <!-- Social media icons -->
      <div class="flex-center">
        <a target="_blank" rel="noopener noreferrer" href="https://github.com/masterdesky/" alt="GitHub"><i class="fa fa-github fa-4x icon-3d"></i></a>
        <a target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/channel/UCBDSB7PdQ3E9l9WSBsTy7cQ/" alt="YouTube"><i class="fa fa-youtube-play fa-4x icon-3d"></i></a>
        <a target="_blank" rel="noopener noreferrer" href="https://www.instagram.com/masterdesky/" alt="Instagram"><i class="fa fa-instagram fa-4x icon-3d"></i></a>
        <a target="_blank" rel="noopener noreferrer" href="https://twitter.com/masterdesky/" alt="Twitter"><i class="fa fa-twitter fa-4x icon-3d"></i></a>
      </div>

      <!-- content start -->
      <h1>Downloads</h1>
      <div class="index-content">
        Content comes here.
      </div>
      <div class="footer">
        <p>
          Website created and designed by Balázs Pál - &copy; 2020
        </p>
      </div> 
    </div>
  </body>
</html>
