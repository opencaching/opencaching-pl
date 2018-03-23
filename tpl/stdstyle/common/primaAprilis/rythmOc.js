
var rythm;

$( document ).ready(function() {
    console.log( "ready!" );

    addTurnOffButton()

    rythm = new Rythm()
    prepeareMusic()
});

function prepeareMusic()
{
  rythm.setMusic('/tpl/stdstyle/common/primaAprilis/rythmC.mp3')
  rythm.setGain(0.1)

  rythm.addRythm('btn', 'borderWidth', 0, 5)
  rythm.addRythm('oc-logo', 'jump1', 0, 10)

  rythm.addRythm('rythm_nav2', 'color', 0, 10, {
    from: [255,0,0], //blue
    to:[127,162,202]
  })

  //menu
  rythm.addRythm('rythm_nav3MainMenu', 'color', 0, 10, {
    from: [148,0,211], //blue
    to:[219,230,241]
  })
  rythm.addRythm('rythm_nav3UserMenu', 'color', 0, 10, {
    from: [0,255,0], //green
    to:[219,230,241]
  })
  rythm.addRythm('rythm_nav3AddsMenu', 'color', 0, 10, {
    from: [255,118,0], //yellow
    to:[219,230,241]
  })


  rythm.addRythm('content2-container','neon', 0, 10, {
    from: [255,0,0],
    to:[255,255,255]
  })

  rythm.addRythm('site-name', 'kern', 0, 10,{ min: -5, max: 5, reverse:true })
  startMusic()
}

function addTurnOffButton(){
  var but = $('<li><a id="rythmBtn" style="color:red; background-color:yellow"></a></li>')
  $('#nav2 ul').append(but)
}

function startMusic()
{
  rythm.start()
  $('#rythmBtn').html("Music OFF")
  $('#rythmBtn').click(function(){stopMusic()})
}

function stopMusic()
{
  rythm.stop()
  $('#rythmBtn').html("Music ON")
  $('#rythmBtn').click(function(){startMusic()})
}