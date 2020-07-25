<?php
require "../../../engine.autoloader.php";
$widgetid = $_REQUEST['widgetid'];
?>
 
<button onclick="$('#tmpbody').hide(); $('#content_body').show();" class="ios-6-arrow left blue" data-title="Back"></button>

<div id="" class="shadow" style="margin: 5px; background-color:white;"><?php include "../../../widget/".$widgetid."/index.php";?></div>

<style type="text/css">
.ios-6-arrow.blue {
  display: -moz-inline-stack;
  display: inline-block;
  vertical-align: middle;
  *vertical-align: auto;
  zoom: 1;
  *display: inline;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background: none;
  position: relative;
  margin: 0;
  padding: 0;
  border: 0;
  height: 26px;
  min-width: 20px;
  cursor: pointer;
  overflow: visible;
}


.ios-6-arrow.blue.left:before {
  -webkit-box-shadow: 1px 2px 1px -2px rgba(0, 0, 0, 0.4) inset, -1px 4px 2px -4px rgba(0, 0, 0, 0.4) inset;
  -moz-box-shadow: 1px 2px 1px -2px rgba(0, 0, 0, 0.4) inset, -1px 4px 2px -4px rgba(0, 0, 0, 0.4) inset;
  box-shadow: 1px 2px 1px -2px rgba(0, 0, 0, 0.4) inset, -1px 4px 2px -4px rgba(0, 0, 0, 0.4) inset;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjEwMCUiIHkxPSIwJSIgeDI9IjAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzk5YWFjMiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzQxNjE4ZCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-image: -webkit-gradient(linear, 100% 0%, 0% 100%, color-stop(0%, #99aac2), color-stop(100%, #41618d));
  background-image: -webkit-linear-gradient(top right, #99aac2, #41618d);
  background-image: -moz-linear-gradient(top right, #99aac2, #41618d);
  background-image: -o-linear-gradient(top right, #99aac2, #41618d);
  background-image: linear-gradient(top right, #99aac2, #41618d);
  -webkit-background-size: 20px 18px;
  -moz-background-size: 20px 18px;
  -o-background-size: 20px 18px;
  background-size: 20px 18px;
  -webkit-transform: rotate(-45deg) scale(0.86) skew(-9deg, -9deg);
  -moz-transform: rotate(-45deg) scale(0.86) skew(-9deg, -9deg);
  -ms-transform: rotate(-45deg) scale(0.86) skew(-9deg, -9deg);
  -o-transform: rotate(-45deg) scale(0.86) skew(-9deg, -9deg);
  transform: rotate(-45deg) scale(0.86) skew(-9deg, -9deg);
  -webkit-transform-origin: 50% 50%;
  -moz-transform-origin: 50% 50%;
  -ms-transform-origin: 50% 50%;
  -o-transform-origin: 50% 50%;
  transform-origin: 50% 50%;
  position: relative;
  display: -moz-inline-stack;
  display: inline-block;
  vertical-align: middle;
  *vertical-align: auto;
  zoom: 1;
  *display: inline;
  top: 0;
  left: 16px;
  width: 22px;
  height: 22px;
  border: 1px solid #f00;
  border-width: 1px;
  border-width: 0 0 1px 1px;
  border-color: #7594bf transparent transparent #7594bf;
  background-position: -2px 0;
  background-repeat: no-repeat;
  content: "";
  -webkit-border-radius: 0 6px 0 6px;
  -moz-border-radius: 0 6px 0 6px;
  -ms-border-radius: 0 6px 0 6px;
  -o-border-radius: 0 6px 0 6px;
  border-radius: 0 6px 0 6px;
  -moz-border-radius-topright: 8px 5px;
  -webkit-border-top-right-radius: 8px 5px;
  border-top-right-radius: 8px 5px;
  -moz-border-radius-bottomleft: 5px 8px;
  -webkit-border-bottom-left-radius: 5px 8px;
  border-bottom-left-radius: 5px 8px;
}

.ios-6-arrow.blue.left:after {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  -webkit-border-radius: 4px 4px 4px 4px;
  -moz-border-radius: 4px 4px 4px 4px;
  -ms-border-radius: 4px 4px 4px 4px;
  -o-border-radius: 4px 4px 4px 4px;
  border-radius: 4px 4px 4px 4px;
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzk5YWFjMiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzQxNjE4ZCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #99aac2), color-stop(100%, #41618d));
  background-image: -webkit-linear-gradient(top, #99aac2, #41618d);
  background-image: -moz-linear-gradient(top, #99aac2, #41618d);
  background-image: -o-linear-gradient(top, #99aac2, #41618d);
  background-image: linear-gradient(top, #99aac2, #41618d);
  -webkit-background-size: 26px 26px;
  -moz-background-size: 26px 26px;
  -o-background-size: 26px 26px;
  background-size: 26px 26px;
  -webkit-box-shadow: 0 2px 2px -2px rgba(0, 0, 0, 0.4) inset, -1px 1px 1px -1px rgba(0, 0, 0, 0.4) inset;
  -moz-box-shadow: 0 2px 2px -2px rgba(0, 0, 0, 0.4) inset, -1px 1px 1px -1px rgba(0, 0, 0, 0.4) inset;
  box-shadow: 0 2px 2px -2px rgba(0, 0, 0, 0.4) inset, -1px 1px 1px -1px rgba(0, 0, 0, 0.4) inset;
  text-shadow: 0 -1px 0 #474747;
  position: relative;
  top: 0;
  left: 2px;
  display: -moz-inline-stack;
  display: inline-block;
  vertical-align: middle;
  *vertical-align: auto;
  zoom: 1;
  *display: inline;
  padding: 5px 10px 5px 5px;
  margin: 0;
  width: auto;
  height: 26px;
  border: 1px solid #7594bf;
  border-width: 0 0 1px 0;
  content: attr(data-title);
  text-decoration: none;
  white-space: nowrap;
  font-weight: 500;
  color: white;
  font-size: 12px;
  font-family: "HelveticaNeue-Bold", "Helvetica Neue Bold", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  line-height: 16px;
}

.ios-6-arrow.blue:hover:after {
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzIxMzE0NyIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzg5OWRiOCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #213147), color-stop(100%, #899db8));
  background-image: -webkit-linear-gradient(top, #213147, #899db8);
  background-image: -moz-linear-gradient(top, #213147, #899db8);
  background-image: -o-linear-gradient(top, #213147, #899db8);
  background-image: linear-gradient(top, #213147, #899db8);
  -webkit-background-size: 26px 26px;
  -moz-background-size: 26px 26px;
  -o-background-size: 26px 26px;
  background-size: 26px 26px;
}
.ios-6-arrow.blue:hover:before {
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjEwMCUiIHkxPSIwJSIgeDI9IjAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzIxMzE0NyIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzg5OWRiOCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-image: -webkit-gradient(linear, 100% 0%, 0% 100%, color-stop(0%, #213147), color-stop(100%, #899db8));
  background-image: -webkit-linear-gradient(top right, #213147, #899db8);
  background-image: -moz-linear-gradient(top right, #213147, #899db8);
  background-image: -o-linear-gradient(top right, #213147, #899db8);
  background-image: linear-gradient(top right, #213147, #899db8);
  -webkit-background-size: 20px 18px;
  -moz-background-size: 20px 18px;
  -o-background-size: 20px 18px;
  background-size: 20px 18px;
}
</style>