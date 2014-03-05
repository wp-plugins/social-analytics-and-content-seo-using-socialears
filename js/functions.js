jQuery(document).ready(function ($) {
    addTitleBtn();
    addAnalyzeBtn();

    var SE_ASSET = SE_ASSET | '/wp-content/plugins/social-analytics-and-content-seo-using-socialears/';

    jQuery("#analyze_btn").click(function () {

        var analyze_url = jQuery("#analyze_url").val();
        var analyze_content = jQuery("#content").val();

        if (analyze_content && analyze_url) {
            jQuery('#analyze_form').remove();
            var tpl = '<form style="display: none;position: absolute" id="analyze_form" method="post" action="' + analyze_url + '" target="_blank"><textarea name="content">' + analyze_content + '</textarea></form>';
            jQuery('body').append(tpl);
            jQuery('#analyze_form').submit();
        }
    });
});


function addTitleBtn() {
    var holder = jQuery("#edit-slug-box"),
        title_url = jQuery("#title_generator_url").val();

    if (!holder.size() || !title_url)
        return;

    var tpl = '<a style="margin-left:10px" class="button button-highlighted" target="_blank" href="' + title_url + '"><img alt="Social ears" style="margin: 0 6px -3px 0" src="' + SE_ASSET + 'images/ears.png">Generate Title</a>'
    holder.append(tpl);
}

function addAnalyzeBtn() {
    var holder = jQuery("#major-publishing-actions");

    if (!holder.size())
        return;

    var tpl = '<div class="misc-pub-section"><input name="analyze" type="button" id="analyze_btn" style="float: right;margin-top: 15px" class="button button-primary button-large" value="Content SEO" /><img alt="Social ears logo" src="' + SE_ASSET + 'images/logo.png"></div>';
    holder.before(tpl);
}