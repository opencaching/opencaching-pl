
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
  rythm.addRythm('nav2', 'color', 0, 10)
  rythm.addRythm('site-name', 'kern', 0, 10,{ min: -5, max: 5 })

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