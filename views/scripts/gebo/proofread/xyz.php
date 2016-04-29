{literal}
<link rel="stylesheet" href="/BO/theme/gebo/js/treeorg/demo/js/jquery/ui-lightness/jquery-ui-1.10.2.custom.css" />
<script type="text/javascript" src="/BO/theme/gebo/js/treeorg/demo/js/jquery/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/BO/theme/gebo/js/treeorg/demo/js/jquery/jquery-ui-1.10.2.custom.js"></script>

<!-- jQuery UI Layout -->
<script type="text/javascript" src="/BO/theme/gebo/js/treeorg/demo/jquerylayout/jquery.layout-latest.min.js"></script>
<link rel="stylesheet" type="text/css" href="/BO/theme/gebo/js/treeorg/demo/jquerylayout/layout-default-latest.css" />

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('body').layout(
            {
                center__paneSelector: "#contentpanel"
            });
    });
</script>
<link href="/BO/theme/gebo/js/treeorg/demo/css/primitives.latest.css?207" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/BO/theme/gebo/js/treeorg/demo/js/primitives.min.js?207"></script>
<script type="text/javascript">
var orgdiagram = null;
var orgdiagram2 = null;

var counter = 0;
var m_timer = null;
var fromValue = null;
var fromChart = null;
var toValue = null;
var toChart = null;
var items = {};

jQuery(document).ready(function () {
    jQuery.ajaxSetup({
        cache: false
    });
    jQuery('#contentpanel').layout(
        {
            center__paneSelector: "#centerpanel"
            , west__size: 100
            , west__paneSelector: "#westpanel"
            , west__resizable: true
            , center__onresize: function () {
            if (orgdiagram != null && orgdiagram2 != null) {
                ResizePlaceholder();
                jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.Refresh);
                jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.Refresh);
            }
        }
        });

    ResizePlaceholder();
    orgdiagram = SetupWidget(jQuery("#orgdiagram"), "orgdiagram");
    orgdiagram2 = SetupWidget(jQuery("#orgdiagram2"), "orgdiagram2");
});
function ImageExist(url)
{
    /*  $.ajax({
     type: 'HEAD',
     url: url,
     success: function() { alert(url);
     return url;
     },
     error: function() {
     return 'http://ep-test.edit-place.com/FO/profiles/bo/clogo/clogo.png';
     }
     });*/
    var img = new Image();
    img.src = url;
    if(img.height != 0)
        return img.src;
    else
        return 'http://ep-test.edit-place.com/FO/profiles/bo/clogo/clogo.png';
}

function SetupWidget(element, name) {
    var result;
    var options = new primitives.orgdiagram.Config();
    //var itemsToAdd = [];
    $.ajax({
        type: "POST",
        url: "/hrm/organisation-char-data",
        data: "parentId=no",
        async:false,
        success: function(result) {
            itemsdatarray = result;
        }

    });
    // var level1usercount = {/literal}{$level1usercount}{literal};
    var itemsToAdd = $.parseJSON(itemsdatarray);
    var datalen = itemsToAdd.length;
    for (var index = 0; index < datalen; index++) {
        //alert(itemsarray[0][index].id);
        id = counter++;
        var imgurl = ImageExist('http://ep-test.edit-place.com/FO/profiles/bo/'+itemsToAdd[index].identifier+'/logo.jpg');
        var newItem = new primitives.orgdiagram.ItemConfig({
            id: itemsToAdd[index].identifier,
            parent: null,
            title: itemsToAdd[index].first_name+' '+itemsToAdd[index].last_name,
            description: itemsToAdd[index].type,
            itemTitleColor: "red",
            image: imgurl
        });
        itemsToAdd.push(newItem);
        items[newItem.id] = newItem;

        if (options.cursorItem == null) {
            options.cursorItem = newItem.id;
        }

        /// getting level 2 user list ////
        $.ajax({
            type: "POST",
            url: "/hrm/organisation-char-data2",
            data: "parentId="+itemsToAdd[index].identifier,
            async:false,
            success: function(result) {
                itemsdatarray2 = result;
            }

        });
        var itemsToAdd2 = $.parseJSON(itemsdatarray2);
        var datalen2 = itemsToAdd2.length;
        if(datalen2 != 0){
            for (var index2 = 0; index2 < datalen2; index2++) {
                id2 = counter++;
                var imgurl = ImageExist('http://ep-test.edit-place.com/FO/profiles/bo/'+itemsToAdd2[index2].identifier+'/logo.jpg');
                var newSubItem = new primitives.orgdiagram.ItemConfig({
                    id: itemsToAdd2[index2].identifier,
                    parent: itemsToAdd[index].identifier,
                    title: itemsToAdd2[index2].first_name+' '+itemsToAdd2[index2].last_name,
                    description: itemsToAdd2[index2].type,
                    image: imgurl
                });
                itemsToAdd.push(newSubItem);
                items[newSubItem.id] = newSubItem;

                /// getting level 3 user list ////
                $.ajax({
                    type: "POST",
                    url: "/hrm/organisation-char-data3",
                    data: "parentId="+itemsToAdd2[index2].identifier,
                    async:false,
                    success: function(result) {
                        itemsdatarray3 = result;
                    }

                });
                var itemsToAdd3 = $.parseJSON(itemsdatarray3);
                var datalen3 = itemsToAdd3.length;
                if(datalen3 != 0){
                    for (var index3 = 0; index3 < datalen3; index3++) {
                        id3 = counter++;
                        var imgurl = ImageExist('http://ep-test.edit-place.com/FO/profiles/bo/'+itemsToAdd3[index3].identifier+'/logo.jpg');
                        var newSubItem3 = new primitives.orgdiagram.ItemConfig({
                            id: itemsToAdd3[index3].identifier,
                            parent: itemsToAdd2[index2].identifier,
                            title: itemsToAdd3[index3].first_name+' '+itemsToAdd3[index3].last_name,
                            description: itemsToAdd2[index2].type,
                            itemTitleColor: "green",
                            image: imgurl
                        });
                        itemsToAdd.push(newSubItem3);
                        items[newSubItem3.id] = newSubItem3;
                    }}
            }}
    }

    options.items = itemsToAdd;
    options.normalLevelShift = 20;
    options.dotLevelShift = 10;
    options.lineLevelShift = 10;
    options.normalItemsInterval = 20;
    options.dotItemsInterval = 10;
    options.lineItemsInterval = 5;
    options.buttonsPanelSize = 48;

    options.pageFitMode = primitives.common.PageFitMode.Auto;
    options.graphicsType = primitives.common.GraphicsType.Auto;
    options.hasSelectorCheckbox = primitives.common.Enabled.True;
    options.hasButtons = primitives.common.Enabled.True;
    options.templates = [getContactTemplate()];
    options.defaultTemplateName = "contactTemplate";
    options.onItemRender = (name == "orgdiagram") ? onOrgDiagramTemplateRender : onOrgDiagram2TemplateRender;
    options.selectCheckBoxLabel = 'Pinned';

    /* chart uses mouse drag to pan items, disable it in order to avoid conflict with drag & drop */
    options.enablePanning = false;

    result = element.orgDiagram(options);

    element.droppable({
        greedy: true,
        drop: function (event, ui) {
            /* Check drop event cancelation flag
             * This fixes following issues:
             * 1. The same event can be received again by updated chart
             * so you changed hierarchy, updated chart and at the same drop position absolutly
             * irrelevant item receives again drop event, so in order to avoid this use primitives.common.stopPropagation
             * 2. This particlular example has nested drop zones, in order to
             * suppress drop event processing by nested droppable and its parent we have to set "greedy" to false,
             * but it does not work.
             * In this example items can be droped to other items (except immidiate children in order to avoid looping)
             * and to any free space in order to make them rooted.
             * So we need to cancel drop  event in order to avoid double reparenting operation.
             */

            if (!event.cancelBubble) {
                toValue = null;
                toChart = name;

                Reparent(fromChart, fromValue, toChart, toValue);

                primitives.common.stopPropagation(event);
            }
        }
    });

    return result;
}

function getContactTemplate() {
    var result = new primitives.orgdiagram.TemplateConfig();
    result.name = "contactTemplate";

    result.itemSize = new primitives.common.Size(140, 100);
    result.minimizedItemSize = new primitives.common.Size(4, 4);
    result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


    var itemTemplate = jQuery(
        '<div class="bp-item bp-corner-all bt-item-frame">'
        + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 136px; height: 20px;">'
        + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 128px; height: 18px;">'
        + '</div>'
        + '</div>'
        + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 50px; height: 60px;">'
        + '<img name="photo" style="height:60px; width:50px;" />'
        + '</div>'
        + '<div name="description" class="bp-item" style="top: 26px; left: 56px; width: 82px; height: 52px; font-size: 10px;"></div>'
        + '</div>'
    ).css({
            width: result.itemSize.width + "px",
            height: result.itemSize.height + "px"
        }).addClass("bp-item bp-corner-all bt-item-frame");
    result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

    return result;
}

function onOrgDiagramTemplateRender(event, data) {
    switch (data.renderingMode) {
        case primitives.common.RenderingMode.Create:
            data.element.draggable({
                revert: "invalid",
                containment: "document",
                appendTo: "body",
                helper: "clone",
                cursor: "move",
                "zIndex": 10000,
                delay: 300,
                distance: 10,
                start: function (event, ui) {
                    fromValue = parseInt(jQuery(this).attr("data-value"), 10);
                    fromChart = "orgdiagram";
                }
            });
            data.element.droppable({
                /* this option is supposed to suppress event propogation from nested droppable to its parent
                 *  but it does not work
                 */
                greedy: true,
                drop: function (event, ui) {
                    if (!event.cancelBubble) {
                        console.log("Drop accepted!");
                        toValue = parseInt(jQuery(this).attr("data-value"), 10);
                        toChart = "orgdiagram";
                        alert('drop'); alert(toValue); alert(fromValue);
                        Reparent(fromChart, fromValue, toChart, toValue);
                        /// logic for the updating the parent when dragged ////
                        $.ajax({
                            type: "POST",
                            url: "/hrm/re-parenting",
                            data: "parentId="+toValue+"&childId="+fromValue,
                            async:false,
                            success: function(result) { //alert(result);
                                smoke.confirm("Are you want proceed with this action",function(e){
                                    if (e)
                                    {
                                        smoke.alert("Successfully changed the parent");
                                        window.location.reload();
                                    }
                                    else
                                    {
                                        return false;
                                        //window.location.reload();
                                    }
                                });

                            }

                        });

                        primitives.common.stopPropagation(event);
                    } else {
                        console.log("Drop ignored!");
                    }
                },
                over: function (event, ui) {
                    toValue = parseInt(jQuery(this).attr("data-value"), 10);
                    toChart = "orgdiagram";

                    /* this is needed in order to update highlighted item in chart,
                     * so this creates consistent mouse over feed back
                     */
                    jQuery("#orgdiagram").orgDiagram({ "highlightItem": toValue });
                    jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.PositonHighlight);
                },
                accept: function (draggable) {
                    /* be carefull with this event it is called for every available droppable including invisible items on every drag start event.
                     * don't varify parent child relationship between draggable & droppable here it is too expensive.
                     */
                    return (jQuery(this).css("visibility") == "visible");
                }
            });
            /* Initialize widgets here */
            break;
        case primitives.common.RenderingMode.Update:
            /* Update widgets here */
            break;
    }

    var itemConfig = data.context;

    /* Set item id as custom data attribute here */
    data.element.attr("data-value", itemConfig.id);

    RenderField(data, itemConfig);
}

function onOrgDiagram2TemplateRender(event, data) {
    switch (data.renderingMode) {
        case primitives.common.RenderingMode.Create:
            data.element.draggable({
                revert: "invalid",
                containment: "document",
                appendTo: "body",
                helper: "clone",
                cursor: "move",
                "zIndex": 10000,
                delay: 300,
                distance: 10,
                start: function (event, ui) {
                    fromValue = parseInt(jQuery(this).attr("data-value"), 10);
                    fromChart = "orgdiagram2";
                }
            });
            data.element.droppable({
                greedy: true,
                drop: function (event, ui) {
                    if (!event.cancelBubble) {
                        console.log("Drop accepted!");
                        toValue = parseInt(jQuery(this).attr("data-value"), 10);
                        toChart = "orgdiagram2";

                        Reparent(fromChart, fromValue, toChart, toValue);
                        primitives.common.stopPropagation(event);
                    } else {
                        console.log("Drop ignored!");
                    }
                },
                over: function (event, ui) {
                    toValue = parseInt(jQuery(this).attr("data-value"), 10);
                    toChart = "orgdiagram2";

                    jQuery("#orgdiagram2").orgDiagram({ "highlightItem": toValue });
                    jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.PositonHighlight);
                },
                accept: function (draggable) {
                    return (jQuery(this).css("visibility") == "visible");
                }
            });
            /* Initialize widgets here */
            break;
        case primitives.common.RenderingMode.Update:
            /* Update widgets here */
            break;
    }

    var itemConfig = data.context;

    data.element.attr("data-value", itemConfig.id);

    RenderField(data, itemConfig);
}

function Reparent(fromChart, value, toChart, toParent) {
    /* following verification needed in order to avoid conflict with jQuery Layout widget */
    if (fromChart != null && value != null && toChart != null) {
        console.log("Reparent fromChart:" + fromChart + ", value:" + value + ", toChart:" + toChart + ", toParent:" + toParent);
        var item = items[value];
        var fromItems = jQuery("#" + fromChart).orgDiagram("option", "items");
        var toItems = jQuery("#" + toChart).orgDiagram("option", "items");
        if (toParent != null) {
            var toParentItem = items[toParent];
            if (!isParentOf(item, toParentItem)) {

                var children = getChildrenForParent(item);
                children.push(item);
                for (var index = 0; index < children.length; index++) {
                    var child = children[index];
                    fromItems.splice(primitives.common.indexOf(fromItems, child), 1);
                    toItems.push(child);
                }
                item.parent = toParent;
            } else {
                console.log("Droped to own child!");
            }
        } else {
            var children = getChildrenForParent(item);
            children.push(item);
            for (var index = 0; index < children.length; index++) {
                var child = children[index];
                fromItems.splice(primitives.common.indexOf(fromItems, child), 1);
                toItems.push(child);
            }
            item.parent = null;
        }
        jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.Refresh);
        jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.Refresh);
    }
}


function getChildrenForParent(parentItem) {
    var children = {};
    for (var id in items) {
        var item = items[id];
        if (children[item.parent] == null) {
            children[item.parent] = [];
        }
        children[item.parent].push(id);
    }
    var newChildren = children[parentItem.id];
    var result = [];
    if (newChildren != null) {
        while (newChildren.length > 0) {
            var tempChildren = [];
            for (var index = 0; index < newChildren.length; index++) {
                var item = items[newChildren[index]];
                result.push(item);
                if (children[item.id] != null) {
                    tempChildren = tempChildren.concat(children[item.id]);
                }
            }
            newChildren = tempChildren;
        }
    }
    return result;
}

function isParentOf(parentItem, childItem) {
    var result = false,
        index,
        len,
        itemConfig;
    if (parentItem.id == childItem.id) {
        result = true;
    } else {
        while (childItem.parent != null) {
            childItem = items[childItem.parent];
            if (childItem.id == parentItem.id) {
                result = true;
                break;
            }
        }
    }
    return result;
};
function RenderField(data, itemConfig) {
    if (data.templateName == "contactTemplate") {
        data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
        data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

        var fields = ["title", "description", "phone", "email"];
        for (var index = 0; index < fields.length; index++) {
            var field = fields[index];

            var element = data.element.find("[name=" + field + "]");
            if (element.text() != itemConfig[field]) {
                element.text(itemConfig[field]);
            }
        }
    }
}
function ResizePlaceholder() {
    var panel = jQuery("#centerpanel");
    var panelSize = new primitives.common.Rect(0, 0, panel.innerWidth(), panel.innerHeight());
    var position = new primitives.common.Rect(0, 0, panelSize.width, panelSize.height);
    position.offset(-2);
    var position2 = new primitives.common.Rect(panelSize.width, 0, panelSize.width, panelSize.height);
    position2.offset(-2);

    jQuery("#orgdiagram").css(position.getCSS());
    jQuery("#orgdiagram2").css(position2.getCSS());
}
</script>
{/literal}
<div id="contentpanel" style="padding: 0px;">
    <!--bpcontent-->

    <div id="centerpanel" style="overflow: hidden; padding-top: 50px; margin: 0px; border: 0px;">
        <div id="orgdiagram" style=" width:100%; overflow: hidden; left: 0px; padding: 0px; margin: 0px; border-style: dotted; border-color: navy; border-width: 1px;"></div>
        <!--            <div id="orgdiagram2" style="position: absolute; overflow: hidden; left: 0px; padding: 0px; margin: 0px; border-style: dotted; border-color: navy; border-width: 1px;"></div>
        -->        </div>
    <!--/bpcontent-->
</div>
