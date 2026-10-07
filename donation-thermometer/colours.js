jQuery(document).ready(function($) {
    if($('#picker').length != 0){
        var p = $('#picker').css('opacity', 1);
        var f = $.farbtastic('#picker');
        var selected;
        $('.colorwell')
          .each(function () { f.linkTo(this); $(this).css('opacity', 0.75); })
          .focus(function() {
            if (selected) {
              $(selected).css('opacity', 0.75).removeClass('colorwell-selected');
            }
            f.linkTo(this);
            p.css('opacity', 1);
            $(selected = this).css('opacity', 1).addClass('colorwell-selected');
          });
          rampColors();
          $('#color_ramp').on("keyup", rampColors);
    }
});

var $jq = jQuery.noConflict();
  
function rampColors(){
	var colors = $jq('#color_ramp').val().split(';');
  var preview = $jq('#rampPreview').empty();
  preview.append($jq('<p>').text('Preview:'));
	$jq.each(colors, function(i, val) {
    var color = val == null ? '' : val.trim();
    if (/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/i.test(color)){
      var svg = $jq('<svg>', { width: 30, height: 50 });
      var rect = $jq('<rect>', { width: 30, height: 50 });
      rect.css({ fill: color, 'stroke-width': 0.2, stroke: 'rgb(0,0,0)' });
      svg.append(rect);
      preview.append(svg);
		}
	})
}
