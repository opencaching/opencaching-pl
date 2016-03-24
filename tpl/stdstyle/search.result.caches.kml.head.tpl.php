<?php echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";
?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <Style id="tradi">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/traditional.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="multi">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/multi.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="myst">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/quiz.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="virtual">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/virtual.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="webcam">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/webcam.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="event">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/event.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="moving">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/moving.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="unknown">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>tpl/stdstyle/images/cache/unknown.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Folder>
            <Name><?php echo $pagetitle; ?></Name>
            <Open>0</Open>
