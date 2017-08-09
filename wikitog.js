// Andy Langton's show/hide/mini-accordion - updated 23/11/2009
// Latest version @ http://andylangton.co.uk/jquery-show-hide
$ej=jQuery.noConflict();
$ej(document).ready(function() {

var showText='Show';
var hideText='Hide';

var is_visible = false;

$ej('.wikitoggle').prev().append(' (<a href="#" class="wikitoggleLink">'+showText+'</a>)');

$ej('.wikitoggle').hide();

$ej('a.wikitoggleLink').click(function() {

is_visible = !is_visible;

$ej(this).html( (!is_visible) ? showText : hideText);

$ej(this).parent().next('.wikitoggle').toggle('slow');

return false;
});
});