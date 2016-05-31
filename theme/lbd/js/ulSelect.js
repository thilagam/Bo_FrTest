$.fn.ulSelect = function(){
  var ul = $(this);

  if (!ul.hasClass('zg-ul-select')) {
    ul.addClass('zg-ul-select');
  }
  // SVG arrow
  var arrow = '';
  //$('li:first-of-type', this).addClass('active').append(arrow);
  $(this).on('click', 'li', function(event){
    // Remove div#selected if it exists
    if ($('#selected--zg-ul-select').length) {
      $('#selected--zg-ul-select').remove();
    }
    // Store that div
    var selected = $('#selected--zg-ul-select');
    // Remove the arrow
    $('li #ul-arrow', ul).remove();
    // Toggle active class on the <ul>
    ul.toggleClass('active');
    // Remove active class from any <li> that has it...
    ul.children().removeClass('active');
    // And add the class to the <li> that gets clicked
    $(this).toggleClass('active');
    // The text of the click <li>
    var selectedText = $(this).text();
    // If the <ul> dropdown is open, activate the div#selected, and append the clicked <li> text (and SVG arrow)
    if (ul.hasClass('active')) {
      selected.text(selectedText).addClass('active').append(arrow);
    }
    else {
      // Remove div#selected
      selected.text('').removeClass('active'); 
      // Add the SVG arrow to the active <li>
      $('li.active', ul).append(arrow);
    }
    });
    // Close the faux select menu when clicking outside it 
    $(document).on('click', function(event){
      if($('ul.zg-ul-select').length) {
       if(!$('ul.zg-ul-select').has(event.target).length == 0) {
        return;
      }
      else {
        $('ul.zg-ul-select').removeClass('active');
        $('#selected--zg-ul-select').removeClass('active').text('');
        $('#ul-arrow').remove();
        $('ul.zg-ul-select li.active').append(arrow);
      } 
    }
  });
}
