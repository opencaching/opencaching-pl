
var rythm;

$( document ).ready(function() {

    addTurnOffButton()

    rythm = new Rythm()
    rythm.oc_prepared = false;

    if(isMusicDisabled()){
      stopMusic()

    }else{
      prepeareMusic()
      startMusic()
    }
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
  rythm.oc_prepared = true;
}

function addTurnOffButton(){
  var but = $('<li><a id="rythmBtn" style="color:red; background-color:yellow"></a></li>')
  $('#nav2 ul').append(but)
}

function startMusic()
{
  if(!rythm.oc_prepared){
    prepeareMusic()
  }
  rythm.start()
  $('#rythmBtn').html("Music OFF &#x1f50a;")
  $('#rythmBtn').click(function(){stopMusic()})

  Cookies.set('rythmOc', 'musicOn');
}

function stopMusic()
{
  rythm.stop()
  $('#rythmBtn').html("Music ON &#x1f50a;")
  $('#rythmBtn').click(function(){startMusic()})

  Cookies.set('rythmOc', 'musicOff');
}

function isMusicDisabled()
{
  if($cookieVal = Cookies.get('rythmOc')){
    return $cookieVal === 'musicOff';
  }

  //cookie not set
  return false;
}
