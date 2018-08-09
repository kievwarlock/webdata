import markers from "../json/markers";

class Marker {

    constructor( map, markerType , markers = false ) {

        this.map = map;
        this.markers = markers;
        this.filterSelector = document.querySelector('#filter-group');

        this.initDefault();

        this.isDragging = false;
        this.isCursorOverPoint = false;
        this.canvas = map.getCanvasContainer();


        this.markerProps = {
            'point':
                {
                    'key': 'point-marker',
                    'color': 'blue',
                    'size': '0.010',
                    'fixSize': '0.010',
                    'circlePoints': 60,
                } ,
            'circle':
                {
                    'key': 'circle-marker',
                    'color': 'blue',
                    'size': '0.020',
                    'circlePoints': 60,
                } ,

        };

        this.errorColor = 'red';

        this.markerRadiusProps = {
            'key': 'radius-layer',
            'color': 'blue',
            'size': '0.025',
            //'fixSize': '0.010',
            'circlePoints': 60,
        };

        this.markerType = 'point';

        let checkedType = document.querySelector('#marker-view-type input:checked');

        if( checkedType ){

            if( this.markerProps[checkedType.value] ){
                this.markerType = checkedType.value;
            }
        }

    }


    checkIntersection( JSONCircle ) {

        var returnIntersect = false;
        var userPolygon = JSONCircle.data.features[0];

        //var userPolygon = JSONCircle;

        var polygonBoundingBox = turf.bbox(userPolygon);
        var southWest = [polygonBoundingBox[0], polygonBoundingBox[1]];
        var northEast = [polygonBoundingBox[2], polygonBoundingBox[3]];
        var northEastPointPixel = this.map.project(northEast);
        var southWestPointPixel = this.map.project(southWest);

        var features = this.map.queryRenderedFeatures( [southWestPointPixel, northEastPointPixel],{
            layers: [
                'water',
                //'polygon',
                'road-primary',
                'road-street',
                'road-service-link-track',
                'road-secondary-tertiary'
            ]
        });

        if( features ){

            $.each(features, function(index, value) {

                if( returnIntersect === false){

                    // Line
                    var intersectionLine = turf.lineIntersect( value, userPolygon);
                    if( intersectionLine.features[0] ) {
                        returnIntersect = true;
                        return;
                    }

                    // Polygon
                    var intersection = turf.intersect( value, userPolygon);
                    if( intersection ) {
                        returnIntersect = true;
                        return;
                    }

                    // Polygon MultiPolygon
                    // Inside polygon

                    if(  userPolygon.geometry.coordinates[0][0]  ){

                        if( value._geometry.type == 'Polygon' || value._geometry.type == 'MultiPolygon'){
                            var booleanPointInPolygon = turf.booleanPointInPolygon(  userPolygon.geometry.coordinates[0][0], value);
                            if( booleanPointInPolygon ) {
                                returnIntersect = true;
                                return;
                            }
                        }
                    }


                }

            });
        }

        return returnIntersect;

    }



    addFilterMarkers ( type , filterSelector ) {

        let map = this.map;
        for (var typeItem of type) {

            let input = document.createElement('input');
            input.type = 'checkbox';
            input.id = typeItem;
            input.checked = true;
            this.filterSelector.appendChild(input);

            var label = document.createElement('label');
            label.setAttribute('for', typeItem);
            label.textContent = typeItem;
            this.filterSelector.appendChild(label);

            input.addEventListener('change', function(e) {
                let currentType = e.target.getAttribute('id');
                map.setLayoutProperty(currentType, 'visibility',
                    e.target.checked ? 'visible' : 'none');
            });

        }

    }

    addDefaultMarkers( data ){

        this.map.addSource("places", {
            "type": "geojson",
            "data": data
        });

        for (var features of data.features) {

            let type = features.properties.icon;

            this.map.addLayer({
                "id": type,
                "source": "places",
                "type": "circle",
                "paint": {
                    "circle-radius": 10,
                    "circle-color": "#007cbf"
                },
                "filter": ["==", "icon", type ]
            });
        }
    }

    createGeoJSONCircle( center, radiusInKm, points) {
        if(!points) points = 64;

        var coords = {
            latitude: center[1],
            longitude: center[0]
        };

        var km = radiusInKm;

        var ret = [];
        var distanceX = km/(111.320*Math.cos(coords.latitude*Math.PI/180));
        var distanceY = km/110.574;

        var theta, x, y;
        for(var i=0; i<points; i++) {
            theta = (i/points)*(2*Math.PI);
            x = distanceX*Math.cos(theta);
            y = distanceY*Math.sin(theta);

            ret.push([coords.longitude+x, coords.latitude+y]);
        }
        ret.push(ret[0]);

        return {
            "type": "geojson",
            "data": {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [ret]
                    }
                }
                ]
            },

        };
    };

    initDefault () {

        let markers = this.markers;
        if( markers ){
            this.addDefaultMarkers( markers );
            let typeArray = [];
            for (var features of markers.features) {
                let type = features.properties.icon;
                typeArray.push(type);
            }
            let uniType = [...(new Set(typeArray))];
            //this.addFilterMarkers( uniType, this.filterSelector );
        }
    }

    deleteMarker(){

        let currentItem = this.map.getSource( this.markerProps[this.markerType].key );
        if( currentItem ){
            this.map.removeLayer( this.markerProps[this.markerType].key );
            this.map.removeSource( this.markerProps[this.markerType].key );
        }

        // Delete radius
        this.deleteMarkerRadius();

    }

    getProp(key){

        if( key  ){
            if(  key == 'size' && this.markerProps[this.markerType].fixSize ){
                return this.markerProps[this.markerType].fixSize;
            }
            return this.markerProps[this.markerType][key] ;
        }

        return false;
    }

    changeProp( key, value ){

        if( key && value ){
            this.markerProps[this.markerType][key] = value;
            return true;
        }
        return false;

    }

    getCenterMarker( markerSource ){

        let center = false;

        if( markerSource ) {
            center = turf.centerOfMass( turf.polygon(markerSource._data.features[0].geometry.coordinates) );
        }

        return center;

    }

    updateMarker( ){

        let markerKey = this.getProp('key');
        var markerCoordinates = this.map.getSource(markerKey);
        if( markerCoordinates ){

            var center = this.getCenterMarker( markerCoordinates );

            let dataJsonCircle = this.createGeoJSONCircle( center.geometry.coordinates , this.getProp('size'), this.getProp('circlePoints') );

            let color = this.getProp('color');
            if( this.checkIntersection(dataJsonCircle) ) {
                color = this.errorColor;
            }

            this.map.setPaintProperty( markerKey, 'fill-color', color );

            markerCoordinates.setData(dataJsonCircle.data);


            return true;
        }

        return false;

    }

    getAddressByCoordinates( lat, lng ){

        var addressFromApi = false ;
        var apiGeocoder = 'https://nominatim.openstreetmap.org/reverse?format=json&lat='+ lat +'&lon='+ lng + '&zoom=18&addressdetails=1';
        fetch(apiGeocoder)
            .then(
                function(response) {
                    if (response.status !== 200) {
                        console.log('Looks like there was a problem. Status Code: ' +
                            response.status);
                        return;
                    }

                    // Examine the text in the response
                    response.json().then(function(data) {


                        if( data.display_name ){
                            addressFromApi = data.display_name;
                            document.querySelector('#api-coordinates').innerHTML = lat +','+ lng;
                            document.querySelector('#api-address').innerHTML  = addressFromApi;

                        }else{
                            addressFromApi = false;
                        }
                        return addressFromApi;

                    });
                }
            )
            .catch(function(err) {
                console.log('Fetch Error :-S', err);
                return addressFromApi;
            });


    }

    addMarker( event = false , lat = false, lng = false  ){

        if( event === false &&  ( lat === false || lng === false )  ){ return false }

        let latMap =  parseFloat( ( lat !== false ? lat : event.lngLat.lat ) );
        let lngMap =  parseFloat( ( lng !== false ? lng : event.lngLat.lng ) ) ;


        let returnMarkerInfo = {
            'coordinates': {
                'lng': lngMap,
                'lat':latMap,
            },
        };
        //let markerProp = this.markerProps[this.markerType];
        this.deleteMarker();

        let dataJsonCircle = this.createGeoJSONCircle( [lngMap, latMap], this.getProp('size') , this.getProp('circlePoints') );

        let color = this.getProp('color');
        if( this.checkIntersection(dataJsonCircle) ) {
            color = this.errorColor;
        }


        this.map.addSource( this.getProp('key') , dataJsonCircle);
        this.map.addLayer({
            "id":  this.getProp('key') ,
            "type": "fill",
            "source": this.getProp('key') ,
            "layout": {},
            "paint": {
                "fill-color": color,
                "fill-opacity": 0.8
            }
        });

        if(event){
            this.addMarkerRadius(event);
        }else{
            this.addMarkerRadius(false, lngMap, latMap );
        }


        return returnMarkerInfo;
    }





    moveMarker( e, drag ) {

        if (!drag) return;
        this.canvas.style.cursor = 'grabbing';
        let dataJsonCircle =  this.createGeoJSONCircle( [e.lngLat.lng, e.lngLat.lat], this.getProp('size'), this.getProp('circlePoints') );

        this.map.getSource( this.getProp('key') ).setData( dataJsonCircle.data );

        let color = this.getProp('color');
        if( this.checkIntersection(dataJsonCircle) ) {
            color = this.errorColor;
        }
        this.map.setPaintProperty( this.getProp('key') , 'fill-color', color );

        // Move radius
        this.moveMarkerRadius(e);

    }


    onMarkerUp( e , drag ) {
        if (!drag) return;
        this.canvas.style.cursor = '';
        this.isDragging = false;
        this.getAddressByCoordinates(  e.lngLat.lat, e.lngLat.lng );
        this.map.off('mousemove', this.moveMarker(e, this.isDragging ) );
    }

    moveMarkerRadius (e) {

        let dataJsonCircle = this.createGeoJSONCircle( [e.lngLat.lng, e.lngLat.lat], this.markerRadiusProps.size ,this.markerRadiusProps.circlePoints );
        this.map.getSource( this.markerRadiusProps.key ).setData( dataJsonCircle.data );

    }



    // radius layer mathods

    checkMarkerSizeRadius ( newSize ) {

        if( newSize < this.getProp('size') ) {
            return this.getProp('size');
        }else{
            return newSize;
        }

    }
    updateMarkerRadius (  ) {

        var radiusLayer = this.map.getSource( this.markerRadiusProps.key );
        if( radiusLayer ){

            var center = this.getCenterMarker( radiusLayer );

            let dataJsonCircle = this.createGeoJSONCircle( center.geometry.coordinates , this.markerRadiusProps.size ,this.markerRadiusProps.circlePoints );

            this.map.setPaintProperty( this.markerRadiusProps.key, 'line-color', this.markerRadiusProps.color );

            radiusLayer.setData( dataJsonCircle.data );

            return true;
        }

        return false;

    }
    deleteMarkerRadius () {

        let radiusLayer = this.map.getSource( this.markerRadiusProps.key );
        if( radiusLayer ){
            this.map.removeLayer( this.markerRadiusProps.key );
            this.map.removeSource( this.markerRadiusProps.key );
        }

    }
    addMarkerRadius(  event = false , lat = false, lng = false ) {



        if( event === false &&  ( lat === false || lng === false )  ){ return false }

        let latMap =  parseFloat( ( lat !== false ? lat : event.lngLat.lat ) );
        let lngMap =  parseFloat( ( lng !== false ? lng : event.lngLat.lng ) ) ;


        let dataJsonCircle = this.createGeoJSONCircle( [lngMap, latMap], this.markerRadiusProps.size ,this.markerRadiusProps.circlePoints );

        this.map.addSource( this.markerRadiusProps.key , dataJsonCircle);
        this.map.addLayer({
            "id":  this.markerRadiusProps.key ,
            "type": "line",
            "source": this.markerRadiusProps.key ,
            "layout": {},
            "paint": {
                "line-color": this.markerRadiusProps.color,
                "line-opacity": 0.3,
                "line-width": 2,
            }
        });

    }

}

export default Marker;





/*

function checkIntersection( JSONCircle ) {

    var returnIntersect = false;

    //var userPolygon = JSONCircle.data.features[0];
    var userPolygon = JSONCircle;



    var polygonBoundingBox = turf.bbox(userPolygon);
    var southWest = [polygonBoundingBox[0], polygonBoundingBox[1]];
    var northEast = [polygonBoundingBox[2], polygonBoundingBox[3]];
    var northEastPointPixel = map.project(northEast);
    var southWestPointPixel = map.project(southWest);

    var features = map.queryRenderedFeatures( [southWestPointPixel, northEastPointPixel],{
        layers: [
            'water',
            'polygon',
            'road-primary',
            'road-street',
            'road-service-link-track',
            'road-secondary-tertiary'
        ]
    });

    if( features ){

        $.each(features, function(index, value) {

            if( returnIntersect === false){

                // Line
                var intersectionLine = turf.lineIntersect( value, userPolygon);
                if( intersectionLine.features[0] ) {
                    returnIntersect = true;
                    return;
                }

                // Polygon
                var intersection = turf.intersect( value, userPolygon);
                if( intersection ) {
                    returnIntersect = true;
                    return;
                }

                // Polygon MultiPolygon
                // Inside polygon
                if(  userPolygon.geometry.coordinates[0][0]  ){
                    if( value._geometry.type == 'Polygon' || value._geometry.type == 'MultiPolygon'){
                        var booleanPointInPolygon = turf.booleanPointInPolygon(  userPolygon.geometry.coordinates[0][0], value);
                        if( booleanPointInPolygon ) {
                            returnIntersect = true;
                            return;
                        }
                    }
                }


            }

        });
    }

    return returnIntersect;

}
function onMove(e) {

    if (!isDragging) return;

    var coords = e.lngLat;


    var JSONCircle =  createGeoJSONCircle( [coords.lng, coords.lat], GeoJSONCircleDefSize, 60);
    var checkInt = checkIntersection(JSONCircle.data.features[0]);
    console.log('checkInt:',checkInt);
    if(checkInt){
        color = 'red';
    }else{
        color = 'blue';
    }
    map.setPaintProperty( markerSourceName, 'fill-color', color );

    //map.setPaintProperty( 'road-service-link-track', 'line-color', 'red');
    /!*map.setPaintProperty( 'road-service-link-track', 'line-width',{
        "base": 1.5,
        "stops": [
            [
                10,
                1
            ],
            [
                16,
                2
            ]
        ]
    });
    *!/




    // Set a UI indicator for dragging.
    canvas.style.cursor = 'grabbing';


    // Update the Point feature in `geojson` coordinates
    // and call setData to the source layer `point` on it.
    map.getSource( markerSourceName ).setData( JSONCircle.data );

}
function onUp(e) {
    if (!isDragging) return;
    var coords = e.lngLat;

    canvas.style.cursor = '';
    isDragging = false;

    // Unbind mouse events
    map.off('mousemove', onMove);
}
function createGeoJSONCircle(center, radiusInKm, points) {
    if(!points) points = 64;

    var coords = {
        latitude: center[1],
        longitude: center[0]
    };

    var km = radiusInKm;

    var ret = [];
    var distanceX = km/(111.320*Math.cos(coords.latitude*Math.PI/180));
    var distanceY = km/110.574;

    var theta, x, y;
    for(var i=0; i<points; i++) {
        theta = (i/points)*(2*Math.PI);
        x = distanceX*Math.cos(theta);
        y = distanceY*Math.sin(theta);

        ret.push([coords.longitude+x, coords.latitude+y]);
    }
    ret.push(ret[0]);

    return {
        "type": "geojson",
        "data": {
            "type": "FeatureCollection",
            "features": [{
                "type": "Feature",
                "geometry": {
                    "type": "Polygon",
                    "coordinates": [ret]
                }
            }
            ]
        },

    };
};
function zoomMarker(event) {

}
function addMarker(event){


    let markerType = getMarkerType();
    let editMode = getEditMode();
    if( editMode == false){
        return false;
    }
    console.log('TYPE:',markerType);

    if( markerType == 'circle'  ){
        console.log('drawControl:',drawControl);
        if( drawControl == true){
            map.removeControl(draw);
            drawControl = false;
        }
        console.log('drawControl:',drawControl);

        var features = map.queryRenderedFeatures(event.point);
        //console.log('Add center:', event.lngLat.lng, event.lngLat.lat);
        // Get type of point
        if( features.length > 0 ){
            var markerName = features[0].layer.id;
            var layerType = features[0].layer.type;
            var layerSource = features[0].layer.source;

            console.log(
                'ID:', markerName,
                'Type:', layerType,
                'Source:', layerSource,
                'all layer',features[0].layer
            ) ;
        }else{
            console.log('Normal label');
        }


        var currentItem = map.getSource(markerSourceName);
        if( currentItem ){
            console.log('LAYER REMOVE');
            map.removeLayer(markerSourceName);
            map.removeSource(markerSourceName);
        }else{
            console.log('FALSE LAYER REMOVE');
        }



        var dataJsonCircle = createGeoJSONCircle( [event.lngLat.lng, event.lngLat.lat], GeoJSONCircleDefSize, 60);

        var color = 'blue';

        var checkInt = checkIntersection(dataJsonCircle.data.features[0]);
        if(checkInt){
            color = 'red';
        }else{
            color = 'blue';
        }

        map.addSource(markerSourceName, dataJsonCircle);

        map.addLayer({
            "id": markerSourceName,
            "type": "fill",
            "source": markerSourceName,
            "layout": {},
            "paint": {
                "fill-color": color,
                //"fill-opacity": 0.6
            }
        });
    }

    if( markerType == 'polygon' ){
        console.log('drawControl:',drawControl);
        if( drawControl == false){
            map.addControl(draw);
            drawControl = true;
        }
        console.log('drawControl:',drawControl);
        if( draw.getAll() ){
            if( draw.getAll().features.length > 1){
                console.log('DEL');
                draw.delete( draw.getAll().features[0].id );
            }
        }

        //map.addControl(draw);
    }

    if( markerType == 'point' ){
        console.log('drawControl:',drawControl);
        if( drawControl == true){
            map.removeControl(draw);
            drawControl = false;
        }


        var currentItem = map.getSource(markerSourceName);
        if( currentItem ){
            map.removeLayer(markerSourceName);
            map.removeSource(markerSourceName);
        }


        var dataJsonCircle = createGeoJSONCircle( [event.lngLat.lng, event.lngLat.lat], 0.010, 60  );

        var color = 'blue';

        var checkInt = checkIntersection(dataJsonCircle.data.features[0]);
        if(checkInt){
            color = 'red';
        }else{
            color = 'blue';
        }

        map.addSource(markerSourceName, dataJsonCircle);

        map.addLayer({
            "id": markerSourceName,
            "type": "fill",
            "source": markerSourceName,
            "layout": {},
            "paint": {
                "fill-color": color,
                //"fill-opacity": 0.6
            }
        });


    }



}

function mouseDown() {

    if (!isCursorOverPoint) return;

    isDragging = true;

    // Set a cursor indicator
    canvas.style.cursor = 'grab';

    // Mouse events
    map.on('mousemove', onMove);
    map.once('mouseup', onUp);

}


map.on('load', function() {


    map.on('zoom', zoomMarker);
    map.on('click', addMarker);



    map.on('mouseenter', markerSourceName, function(event) {
        //map.setPaintProperty(markerSourceName, 'fill-color', '#3bb2d0');
        canvas.style.cursor = 'move';
        isCursorOverPoint = true;
        map.dragPan.disable();


    });

    map.on('mouseleave', markerSourceName, function() {
        // map.setPaintProperty(markerSourceName, 'fill-color', '#3887be');
        canvas.style.cursor = '';
        isCursorOverPoint = false;
        map.dragPan.enable();

        //popup.remove();
    });

    map.on('mousedown', mouseDown);

//setProximity
    geocoder.on('result', function(ev) {
        var searchResult = 'Type:'+ev.result.geometry.type+'<br> DATA:'+ ev.result.geometry.coordinates;
        $('.output-search-adress').html(searchResult);
        //map.getSource('earthquakes').setData(ev.result.geometry);
    });

});
*/





/*function cube(x) {
    return x * x * x;
}*/
//const foo = Math.PI + Math.SQRT2;
//export { cube, foo };