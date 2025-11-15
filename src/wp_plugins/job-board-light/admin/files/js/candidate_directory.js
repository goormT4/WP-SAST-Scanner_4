var cat = document.querySelector('#categories .icon');


window.addEventListener('click', function(e){
  if (document.querySelector('#categories .icon').contains(e.target)){
    //clicked inside the div
    document.querySelector('#categories .searchbar').style.zIndex = "5";
    document.querySelector('#categories .icon i').style.color = "#fff";
    cat.classList.add('show_searchbar');
  } else{
    // Clicked outside the box
    console.log('outside');
    document.querySelector('#categories .searchbar').style.zIndex = "-1";
    document.querySelector('#categories .icon i').style.color = "#63ba16";
    cat.classList.remove('show_searchbar');
  }
});

window.addEventListener('click', function(e){
  if (document.querySelector('#locations .icon').contains(e.target)){
    //clicked inside the div
    document.querySelector('#locations .searchbar').style.zIndex = "5";
    document.querySelector('#locations .icon i').style.color = "#fff";
    document.querySelector('#locations .icon').classList.add('show_searchbar');
  } else{
    // Clicked outside the box
    console.log('outside');
    document.querySelector('#locations .searchbar').style.zIndex = "-1";
    document.querySelector('#locations .icon i').style.color = "#63ba16";
    document.querySelector('#locations .icon').classList.remove('show_searchbar');
  }
});


var x = window.matchMedia("(max-width: 375px)")

var filter = document.getElementById('filter_search');
filter.addEventListener('click', function() {
  document.getElementById('sidebar').style.marginLeft = "0";
   document.getElementById('close_btn').style.display = "block";
});

document.getElementById('close_btn').addEventListener('click', function(){

  if (x.matches) { // If media query matches
   document.getElementById('sidebar').style.marginLeft = "-340px";
 } else {
  document.getElementById('sidebar').style.marginLeft = "-740px";
 }
});
