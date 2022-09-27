<?php

// header.php
//
// Hervey Allen for ISOC, July 2003
//
// Updated September, 2003
// Update October 24, 2006 to put in place new navigation bar
// Update November 2008 for new site look and feel
// Update March 2013 for nsrc.org
//

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Network Startup Resource Center (NSRC)- Network Education and Training calendar of Events</title>

<link rel="shortcut icon" href="/gifs/favicon.ico" type="image/x-icon" />

<style>
  @import url("/sites/all/modules/uobanner/css/uobanner-styles.css?nmeibl");
</style>

<style media="screen">
  @import url("/sites/all/themes/adaptivetheme/at_core/css/at.settings.style.headings.css?nmeibl");
  @import url("/sites/all/themes/adaptivetheme/at_core/css/at.settings.style.image.css?nmeibl");
  @import url("/sites/all/themes/adaptivetheme/at_core/css/at.settings.style.floatblocks.css?nmeibl");
  @import url("/sites/all/themes/adaptivetheme/at_core/css/at.layout.css?nmeibl");
  @import url("/sites/all/themes/nsrc/css/global.base.css?nmeibl");
  @import url("/sites/all/themes/nsrc/css/global.styles.css?nmeibl");
  @import url("/sites/all/themes/nsrc/css/custom.css?nmeibl");
</style>
<!-- Keeps Site Within 1140px width Container -->
<link type="text/css" rel="stylesheet" href="/sites/default/files/adaptivetheme/nsrc_files/nsrc.responsive.layout.css?nmeibl" media="only screen" />

<style media="screen">
@import url("/sites/default/files/adaptivetheme/nsrc_files/nsrc.fonts.css?nmeibl");
</style>
<style>
@import url("/sites/default/files/adaptivetheme/nsrc_files/nsrc.menutoggle.css?nmeibl");
</style>

<style>
ul.cal {
list-style-image: none;
margin: 0;
padding: 0;
line-height: 0;
list-style-type: circle;
margin-left: 2;
</style>

<!-- CAN REMOVE RESPONSIVE FILES IF NECESSARY, BUT MAY LOOK BETTER WITH THEM -->
<link type="text/css" rel="stylesheet" href="/sites/all/themes/nsrc/css/responsive.custom.css?nmeibl" media="only screen" />
<link type="text/css" rel="stylesheet" href="/sites/all/themes/nsrc/css/responsive.smalltouch.portrait.css?nmeibl" media="only screen and (max-width:320px)" />
<link type="text/css" rel="stylesheet" href="/sites/all/themes/nsrc/css/responsive.smalltouch.landscape.css?nmeibl" media="only screen and (min-width:321px) and (max-width:580px)" />
<link type="text/css" rel="stylesheet" href="/sites/all/themes/nsrc/css/responsive.tablet.portrait.css?nmeibl" media="only screen and (min-width:581px) and (max-width:768px)" />
<link type="text/css" rel="stylesheet" href="/sites/all/themes/nsrc/css/responsive.tablet.landscape.css?nmeibl" media="only screen and (min-width:769px) and (max-width:1024px)" />
<link type="text/css" rel="stylesheet" href="/sites/all/themes/nsrc/css/responsive.desktop.css?nmeibl" media="only screen and (min-width:1025px)" />
<!-- CUSTOM CSS FILE - KEEP THIS -->
<style media="screen">
@import url("/sites/default/files/adaptivetheme/nsrc_files/nsrc.custom.css?nmeibl");
</style>
<link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Oswald:300,300italic,400,400italic,700,700italic" media="all" />

<!-- IE SPECIFIC - KEEP THIS -->
<!--[if lt IE 9]>
<style media="screen">
@import url("/sites/default/files/adaptivetheme/nsrc_files/nsrc.lt-ie9.layout.css?nmeibl");
</style>
<![endif]-->

<!-- Table specific css. Need to adjust as it's overriding fonts in the header -->
<!-- calendar specific version of css to mix with drupal css -->
<!-- <link href="/calendar/css/cal.css" rel="stylesheet" type="text/css"> -->
<!-- Original calendar css that overrides some of the Drupal css -->
<link href="/calendar/css/site.css" rel="stylesheet" type="text/css">

</head>
<body>

<div id="uobanner-container-black">
        <div id="uobanner" style='max-width: 100%;'>
        <div id="uobanner-logobox"><a href="https://www.uoregon.edu">
                <img src="/sites/all/modules/uobanner/images/uologo.png" alt='University of Oregon.' height='35' width='216' /></a>
        </div>
        <div id="uobanner-links">
                <span class="uobanner-text-right"><a href="https://www.uoregon.edu" class="uobanner-uonav">UO Home</a>&nbsp; | &nbsp;
            <a href="https://www.uoregon.edu/azindex/" class="uobanner-uonav">Dept Index</a></span>
        </div>
    </div>
</div>

<div id="page-wrapper">
  <div id="page" class="container page ssc-n ssw-b ssa-l sss-n btc-n btw-b bta-l bts-n ntc-n ntw-b nta-l nts-n ctc-n ctw-b cta-l cts-n ptc-n ptw-b pta-l pts-n at-mt">
<?php
// div id=page-wrapper closed in footer.php
// div id= page closed in footer.php
?>

<div id="menu-bar" style="text-align: left;" class="nav clearfix"><section id="block-block-7" class="block block-block menu-wrapper menu-bar-wrapper clearfix odd first block-count-1 block-region-menu-bar block-7" >
      <h2 class="element-invisible block-title">Logo</h2>

  <a href="/" title="Network Startup Resource Center">
  <img src="/sites/default/files/nsrc-main-logo-transparent.png" alt="nsrc logo" /></a>
  </section><nav id="block-system-main-menu" class="block block-system block-menu no-title menu-wrapper menu-bar-wrapper clearfix even block-count-2 block-region-menu-bar block-main-menu"  role="navigation">

  <ul class="menu clearfix"><li class="leaf menu-depth-1 menu-item-385"><a href="/about" title=""><span>ABOUT</span></a></li><li class="leaf menu-depth-1 menu-item-386"><a href="/activities" title=""><span>ACTIVITIES</span></a></li><li class="leaf menu-depth-1 menu-item-387"><a href="/news" title=""><span>NEWS</span></a></li><li class="leaf menu-depth-1 menu-item-389"><a href="/supporters" title=""><span>SUPPORTERS</span></a></li><li class="last leaf menu-depth-1 menu-item-390"><a href="/calendar" title=""><span>calendar</span></a></li><li class="leaf menu-depth-1 menu-item-391"><a href="/videos" title=""><span>Videos</span></a></li></ul>
</nav>
<section id="block-google-cse-google-cse" class="block block-google-cse menu-wrapper menu-bar-wrapper clearfix odd last block-count-3 block-region-menu-bar block-google-cse">

<script>
  (function() {
    var cx = '016604752275707037582:gn9q3tfmx6w';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' :
'http:') +
        '//cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:search></gcse:search>

</section>
</div> <!-- close div id=menu-bar -->
</div>
<style>
table.gsc-search-box td{padding: 6px 3px !important;}
</style><!-- close id=block-google-cse-google-cse -->
</div> <!-- close div id=menu-bar -->

  </div>

<?php
 // End header.php
?>
