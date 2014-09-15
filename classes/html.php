<?php

function top($otsikko = null) {
    ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
    <html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
        <!-- The above DOCTYPE and html lines and the meta http-equiv line below the head are critical. DO NOT DELETE them -->
        <head>
            <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
            <title>Luonnontieteilijöiden jouluristeily 2013 - Helsingin yliopiston yhteislähtö</title>
            <style type="text/css" media="screen">@import url("css/noodle_real.css");</style>
            <link rel="stylesheet" type="text/css" href="css/css.css" />
            <!-- import with quotes and brackets hides css from:
            Netscape 4.x
            Win IE 3
            Win IE 4
            Mac IE 4.01
            Mac IE 4.5
            Konqueror 2.1.2
            Win Amaya 5.1
            isn't that nice to know? -->
            <style type="text/css">
                /******************************************
                Copyright Notice: Parts of these notes are
                (c) Owen Briggs @ www.thenoodleincident.com
                (for the font css file) (c) Big John @
                www.positioniseverything.net and (c) Paul
                O'Brien @ www.pmob.co.uk, all of whom
                contributed significantly to the design of
                the css and html code.
                All other content is (c) ClevaTreva Designs
                ******************************************/
                /***XXXXXXXXXXXXXXX Primary layout rules XXXXXXXXXXXXXXXXXX XXXXXXXXXXXXXXXXXXX
                
                To change many of the widths/heights requires that other certain values must
                also be changed at the same time. For this reason, beside these critical
                attributes (or nearby if comment hacks do not allow) are comments with the
                calculations as to how to adjust them.
                
                These guidance comments start with /*** and end with ***/

                /***
                You can delete these if you want, but do not delete MAC Hack comments (see
                below).
                
                To change the width of the entire layout, adjust the columns that make up the
                total, remembering the borders. Remember, even one small mistake will degrade
                or even break the layout, so be very careful!
                
                For spacing within the cols, it's best to apply margins on content elements
                inserted into the cols, because padding directly on those col elements will
                change their widths, breaking the layout.
                
                Certain hiding hacks have been used extensively in this layout, so here is a
                quick explanation of them.
                
                The Safari escape tab hack:
                ***************************
                
                 (used on wrapper, and the 3 backgrounds for Moz
                and Opera).
                
                Puts an escape in front of a valid number in the style name to replace a
                letter in that name, e.g. \65 is an e. A tab is then inserted (not a space).
                The purpose of this hack is to hide some code from Safari. Unfortunately,
                some other browsers (like IE) see it for what it should be (but NOT Safari),
                and so we must undo the code for those browsers by other hacks.
                
                The Tan hack:
                *************
                
                * html .anyelement {rules read only by IE/Win and IE/Mac}
                
                The MAC hack:
                *************
                
                (first the active comment you are reading now must be
                closed...) ***/

                /* \*/

                /* */

                /***...Back in comment mode now. Anything between those two comment lines
                will be hidden from IE/Mac. Don't use any comments within this hack or it will
                close prematurely and IE/Mac will begin reading before it should.
                
                The above two hacks are combined so as to feed rules only to IE/Win.
                
                The Holly Hack:
                ***************
                
                Proper use of backslash escape characters inside property names used in the
                Holly hack can further segregate rules to be read by only IE6 from rules for
                IE5.x/Win.
                
                These hiding hacks, along with several other fixes, make possible this formerly
                impossible layout. It is highly unlikely that new browsers will have any
                problem with these valid hiding hacks, and we will have to wait for Microsoft
                to release IE7.
                
                If enabled in the PageMaker, the 100% height in the html and body styles makes
                the design full height. It also breaks Moz because you should use min-height,
                but that doesn't work! Note
                how these 100% heights are hidden from IE Mac with
                the MAC Hack, otherwise they break it.
                
                XXXXXXXXXXXXXXX XXXXXXXXXXXXXXX XXXXXXXXXXXXXXX XXXXXXXXXXXXXXX XXXXXXXXXXXXXXXX ***/
                html,body{
                    margin:0; /*** Do NOT set anything other than a left margin for the page
                  as this will break the design ***/
                    padding:0;
                    border:0;
                    /* \*/
                    height:100%;
                    /* Last height declaration hidden from Mac IE 5.x */
                }
                body{
                    background:#3399FF;
                    min-width:631px; /*** This is needed for moz. Otherwise, the header and footer will
                  slide off the left side of the page if the screen width is narrower than the design.
                  Not seen by IE. Left Col + Right Col + Center Col + Both Inner Borders + Both Outer Borders ***/
                    text-align:center; /*** IE/Win (not IE/MAC) alignment of page ***/
                }
                .clear{
                    clear:both;
                    /*** these next attributes are designed to keep the div
                    height to 0 pixels high, critical for Safari and Netscape 7 ***/
                    height:1px;
                    overflow:hidden;
                    line-height:1%;
                    font-size:0px;
                    margin-bottom:-1px;
                }
                * html .clear{height:auto;margin-bottom:0} /*** stops IE browsers from displaying
                the clear div/br in the page, as these are for Moz/Opera and
                Safari only. If IE 5.x Win DID display these, the page is too high ***/
                #fullheightcontainer{
                    margin-left:auto; /*** Mozilla/Opera/Mac IE 5.x alignment of page ***/
                    margin-right:auto; /*** Mozilla/Opera/Mac IE 5.x alignment of page ***/
                    text-align:left; /*** IE Win re-alignment of page if page is centered ***/
                    position:relative; /*** Needed for IE, othewise header and footer aren't contained
                  directly above and below the body ***/
                    width:631px; /*** Needed for Moz/Opera to keep page from sliding to left side of
                  page when it calculates auto margins above. Can't use min-width. Note that putting
                  width in #fullheightcontainer shows it to IE and causes problems, so IE needs a hack
                  to remove this width. Left Col + Right Col + Center Col + Both Inner Border + Both Outer Borders ***/
                    /* \*/
                    height:100%;
                    /* Last height declaration hidden from Mac IE 5.x */
                    /*** Needed for Moz to give full height design if page content is
                    too small to fill the page ***/
                }
                #wrapper{
                    min-height:100%; /*** moz uses this to make full height design. As this #wrapper
                  is inside the #fullheightcontainer which is 100% height, moz will not inherit heights
                  further into the design inside this container, which you should be able to do with
                  use of the min-height style. Instead, Mozilla ignores the height:100% or
                  min-height:100% from this point inwards to the center of the design - a nasty bug.
                  If you change this to height:100% moz won't expand the design if content grows.
                  Aaaghhh. I pulled my hair out over this for days. ***/
                    /* \*/
                    height:100%;
                    /* Last height declaration hidden from Mac IE 5.x */
                    /*** Fixes height for non moz browsers, to full height ***/
                    border-right:1px solid #000000; /*** Sets the external right side border. ***/
                    border-left:1px solid #000000; /*** Sets the external left side border. ***/
                    background:#33CCFF; /*** Set background color for side columns for Safari and IE ***/
                }
                #wrapp\65	r{ /*** for Opera and Moz (and some others will see it, but NOT Safari) ***/
                    height:auto; /*** For moz to stop it fixing height to 100% ***/
                }
                /* \*/
                * html #wrapper{
                    height:100%;
                }
                /* Last style with height declaration hidden from Mac IE 5.x */
                /*** Fixes height for IE, back to full height,
                from esc tab hack moz min-height solution ***/
                #outer{
                    z-index:1; /*** Critical value for Moz/Opera Background Column colors fudge to work ***/
                    position:relative; /*** IE needs this or the contents won't show outside the parent container. ***/
                    margin-left:150px; /*** Critical left col dimension value = left col width ***/
                    width:478px; /*** Critical left and right col/divider dimension value (moves inversly) = center col width ***/
                    border-left:1px solid #000000; /*** Sets the internal left side border. ***/
                    background:#ADD8E6; /*** Sets background of center col***/
                    /* \*/
                    height:100%;
                    /* Last height declaration hidden from Mac IE 5.x */
                    /*** Needed for full height inner borders in Win IE ***/
                }
                /*** The next style hack for widths are NOT needed if no internal side borders are needed ***/
                * html #outer{ /*** IE5.x/win box model fix ***/
                    width:479px; /*** Critical left and right col/divider dimension value
                  (moves inversly) = Center Col Width + Both Inner Borders ***/
                    width:478px; /*** Critical left and right col/divider dimension value (moves inversly) = Center Col Width ***/
                }
                #left{
                    width:152px; /*** Critical left col/divider dimension value = Left Col Width + 1px + One Internal Border Width ***/
                    float:left;
                    display:inline;
                    position:relative; /*** IE needs this or the contents won't show
                  outside the parent container. ***/
                    margin-left:-151px; /*** Critical left col/divider dimension value = left col width + one internal border width ***/
                }
                *>html #left{width:151px;} /*** Fix only for IE/Mac = left col width + one internal border width ***/
                #container-left{
                    width:150px; /*** Critical left col dimension value = left col width - 1px ***/
                }
                /*** Static fixes ***/

                /*** a Note on the Holly hack: if IE/Win shows bugs it's a good idea to apply the height:1%
                     hack to different elements and see if that fixes the problem. Sometimes it may be
                     necessary to use "position: relative;" on certain elements, but it's hard to tell in
                     advance which elements will need such fixes. ***/
                /*** This is a STATIC fix for IE5/Win at the largest text size setting. ***/
                /* \*/
                * html #left{margin-right:-3px;}
                /* Above style hidden from Mac IE */
                /*** All the IE fixes that are inside seperate "Mac-hacks" may be grouped within
                     just one Mac-hack for convenience if desired. However, each fix must come
                     later than the rule it is fixing or the fix itself will be overridden. ***/
                #center{
                    width:478px; /*** Set to = center col width ***/
                    float:right;
                    display:inline;
                    /* \*/
                    margin-left:-1px;
                    /* Hidden from IE-mac */
                }
                /*** clearheader heights are made from header height + borders +
                any sidebar box height, less any sidebar intrusion.
                Similar calcs for footers. ***/
                #clearheadercenter{
                    height:72px; /*** needed to make room for header in center column ***/
                    overflow:hidden;
                }
                #clearheaderleft{
                    height:72px; /*** needed to make room for header in left column ***/
                    overflow:hidden;
                }
                #clearfootercenter{
                    height:52px; /*** needed to make room for footer in center column ***/
                    overflow:hidden;
                }
                #clearfooterleft{
                    height:52px; /*** needed to make room for footer in left column ***/
                    overflow:hidden;
                }
                #footer{
                    z-index:1; /*** Critical value for Moz/Opera Background Column colors fudge to work ***/
                    position:absolute;
                    clear: both;
                    width:631px; /*** Set to Left Col + Right Col + Center Col + Both Inner Borders +
                  Both External Borders ***/
                    height:52px; /*** = Bottom Margin + One Outer Border + body to footer divider depth +
                  subfooter1 height + any other subfooter heights ***/
                    overflow:hidden;
                    margin-top:-52px; /*** negative height ***/
                }
                #subfooter1{
                    background:#33FFFF; /*** Background Color of Sub-footer #1 ***/
                    text-align:center;
                    margin:0 1px; /*** Margin to show left and right External Borders - all sub-headers and sub-footers ***/
                    height:50px; /*** sub-footer row height ***/
                }
                #header{
                    z-index:1; /*** Critical value for Moz/Opera Background Column colors fudge to work ***/
                    position:absolute;
                    top:0px;
                    width:631px; /*** Set to Left Col (not if left sidebar fully intrudes into header or left sidebar is off)
                  + Right Col (not if right sidebar fully intrudes into header or right sidebar is off) + Center Col + Both Inner
                  Borders (not if any sidebar intrudes into header or footer, or Inner Borders are off) + Both External Borders
                  (not if external borders are off) ***/
                    height:72px; /*** = Top Margin + One Outer Border + header to body divider depth +
                  subheader1 height + any other subheader heights ***/
                    overflow:hidden;
                }
                .outer_horiz_border, .sb_outer_horiz_border{
                    background:#000000;
                    height:1px;
                    overflow:hidden;
                    font-size:0px
                }
                #subheader1{
                    background:#33FFFF; /*** Background Color of Sub-header #1 ***/
                    text-align:center;
                    margin:0 1px; /*** Margin to show left and right External Borders - all sub-headers and sub-footers ***/
                    height:70px; /*** sub-header row height ***/
                }
                #gfx_bg_middle{
                    top:0px;
                    position:absolute;
                    height:100%;
                    overflow:hidden;
                    width:478px; /*** = Center Col Width ***/
                    margin-left:150px; /*** = Left Col Width ***/
                    background:#ADD8E6; /*** Set background color for center column for Mozilla and Opera ***/
                    border-left:1px solid #000000;
                    border-left:1px solid #000000;
                }
                * html #gfx_bg_middle{
                    display:none; /*** Hides the moz fix from IE ***/
                }
            </style>
            <!--[if IE]>
            <style type="text/css">
            /*** The rule below prevents long urls from widening floated cols and breaking the layout
                 in IE. It is not W3C valid, but if placed within a "Conditional comment" it will be hidden
                 from all user agents other than IE/Win, and thus validate. This fix fails in IE5/Win.
                 http://msdn.microsoft.com/workshop/author/dhtml/overview/ccomment_ovw.asp ***/
            #outer{word-wrap:break-word;}
            </style>
            <![endif]-->
        </head>
        <body>
            <div id="fullheightcontainer">
                <div id="wrapper">
                    <div id="outer">
                        <div id="center">
                            <div id="clearheadercenter"></div>
                            <div id="container-center">
            <?php
            if($otsikko != null)
                echo '<h2>' . $otsikko .'</h2>';
        }

        function bottom() {
            ?>
        </div>
                            <div id="clearfootercenter"></div>
                        </div>
                        <div id="left">
                            <div id="clearheaderleft"></div>
                            <div id="container-left">
                                <ul>
<li class="linkki"><a href="index.php">Etusivu</a></li>
</ul>
                            </div>

                            <div id="clearfooterleft"></div>
                        </div>
                        <div class="clear">&nbsp;</div>
                    </div>
                    <div id="gfx_bg_middle">&nbsp;</div>
                </div>
                <div id="header">
                    <div class="outer_horiz_border">&nbsp;</div>
                    <div id="subheader1">
                        <p class="otsikko">Jokin Tapahtuma<br />Jonkin yhteislähtö</p>
                    </div>
                    <div class="outer_horiz_border">&nbsp;</div>
                </div>
                <div class="clear">&nbsp;</div>
                <div id="footer">
                    <div class="outer_horiz_border">&nbsp;</div>
                    <div id="subfooter1">
                        <p style="display:block; width:300px; float:left;"><i>Järjestäjä A / Järjestäjä B<br />testi@email.fi</i></p>
                        <p style="display:block; width:300px; float:left;"><i>Ilmojärjestelmän on tehnyt Tatu Tallberg<br />(ulkoasusta saa valittaa)</i></p>
                    </div>
                    <div class="outer_horiz_border">&nbsp;</div>
                </div>
            </div>
        </body>
    </html>
    <?php
}
?>
