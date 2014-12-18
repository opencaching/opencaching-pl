<?php echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";
?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <Style id="tradi">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/tradi.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="multi">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/multi.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="myst">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/myst.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="virtual">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/virtual.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="webcam">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/webcam.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="event">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/event.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="moving">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/moving.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Style id="unknown">
            <IconStyle>
            <Icon>
            <href><?php echo $absolute_server_URI; ?>images/ge/unknown.png</href>
            </Icon>
            </IconStyle>
        </Style>
        <Folder>
            <Name><?php echo $pagetitle; ?></Name>
            <Open>0</Open>
