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
           /* layers: [
                'water',
                //'polygon',
                'road-primary',
                'road-street',
                'road-service-link-track',
                'road-secondary-tertiary'
            ]*/
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


        //this.map.addSource( this.getProp('key') , dataJsonCircle);

        this.map.addSource( this.getProp('key') , {
            type: 'geojson',
            data: {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "properties": {},
                    "geometry": {
                        "type": "Point",
                        "coordinates": [
                            lngMap,
                            latMap
                        ]
                    }
                }]
            }
        });

        this.map.addLayer({
            "id":  this.getProp('key') ,
            "source": this.getProp('key') ,
            "type": "circle",
            "paint": {
                "circle-radius": 10,
                "circle-color": "#d9534f"
            }
        });
/*

        if(event){
            this.addMarkerRadius(event);
        }else{
            this.addMarkerRadius(false, lngMap, latMap );
        }
*/


        return returnMarkerInfo;
    }


    pushMarkerImport( event = false , lat = false, lng = false  ){

        if( event === false &&  ( lat === false || lng === false )  ){ return false }

        let latMap =  parseFloat( ( lat !== false ? lat : event.lngLat.lat ) );
        let lngMap =  parseFloat( ( lng !== false ? lng : event.lngLat.lng ) ) ;


        let returnMarkerInfo = {
            'coordinates': {
                'lng': lngMap,
                'lat':latMap,
            },
        };


        let importId = document.querySelectorAll(".map-selection-import-data input").length;
        let layerId = 'importMarker'+importId;

        this.map.addSource( layerId , {
            type: 'geojson',
            data: {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "properties": {},
                    "geometry": {
                        "type": "Point",
                        "coordinates": [
                            lngMap,
                            latMap
                        ]
                    }
                }]
            }
        });

        this.map.addLayer({
            "id":  layerId ,
            "source": layerId,
            "type": "circle",
            "paint": {
                "circle-radius": 5,
                "circle-color": "#73d941"
            }
        });


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


const ACCESS_TOKEN  =  'pk.eyJ1IjoiZ2V0aGVyb25lIiwiYSI6ImNqaDZlYjR3MzFtMGoycWxua3p0cjFhdzMifQ.me2KR-YMWtpyLOX840116A';
const MAP_STYLE     =   'mapbox://styles/getherone/cjhgn3prm3psf2sryr59nt8zz';
const MAP_CONTAINER = 'map';
const INIT_MAP_CENTER = [ 30.29, 50.43 ];
const DEFAULT_MAP_ZOOM = 8;
const DEFAULT_MAP_MIN_ZOOM = 1;
const DEFAULT_MAP_MAX_ZOOM = 20;


function initMap( lat, lng ){


    if( document.getElementById( MAP_CONTAINER ) ) {



        mapboxgl.accessToken = ACCESS_TOKEN;
        var map = new mapboxgl.Map({
            container: MAP_CONTAINER,
            style: MAP_STYLE,
            center: INIT_MAP_CENTER,
            zoom: DEFAULT_MAP_ZOOM,
            minZoom:DEFAULT_MAP_MIN_ZOOM,
            maxZoom:DEFAULT_MAP_MAX_ZOOM,
        });


        map.on('load', function() {

            var initMarker = new Marker(map, 'point');
            initMarker.addMarker('', lat, lng);


            map.on('click', function (e) {


                let currentCoordinates = initMarker.addMarker(e);


                $('#geo-point-latitude').val(currentCoordinates.coordinates.lat);
                $('#geo-point-longitude').val(currentCoordinates.coordinates.lng);

            });

        });



    }

}

if( document.getElementById( MAP_CONTAINER ) ) {



    mapboxgl.accessToken = ACCESS_TOKEN;
    var map = new mapboxgl.Map({
        container: MAP_CONTAINER,
        style: MAP_STYLE,
        center: INIT_MAP_CENTER,
        zoom: DEFAULT_MAP_ZOOM,
        minZoom:DEFAULT_MAP_MIN_ZOOM,
        maxZoom:DEFAULT_MAP_MAX_ZOOM,
    });



    map.on('load', function() {

        var initMarker = new Marker(map, 'point');

        map.on('click', function (e) {

            //initMarker.updateMarker();
            let currentCoordinates = initMarker.addMarker(e);

            let importData = document.querySelector(".map-selection-import-data");
            if( importData ){
                let input = document.createElement("input");
                input.type = "text";
                input.name = "markersCoordinates[]";
                input.value = currentCoordinates.coordinates.lat + ',' + currentCoordinates.coordinates.lng;
                importData.appendChild(input);
                initMarker.pushMarkerImport(e );
            }


            $('.map-lat').val(currentCoordinates.coordinates.lat);
            $('.map-lng').val(currentCoordinates.coordinates.lng);

            //$('.map-export-coordinates').html('lng:' + currentCoordinates.coordinates.lat + ' ; lng:' + currentCoordinates.coordinates.lng);
            //console.log(currentCoordinates);

        });


        /*SEARCH*/
        var classItemResult = 'search-item-result';
        //let seeachResultItem = document.querySelector('.search-item-result');
        //if( seeachResultItem ){
        document.addEventListener( "click", seeachResultListener );

        function seeachResultListener(event){
            var element = event.target;
            if(element.className == classItemResult ){
                let resultAddress = element.innerHTML;
                let resultLon = element.getAttribute('data-lon');
                let resultLat = element.getAttribute('data-lat');
                document.querySelector('.search-input').value = resultAddress;
                map.flyTo( {center: [resultLon, resultLat ], zoom: 14} );
                //initMarker.updateMarker();
                initMarker.addMarker( false, resultLat,resultLon);
                document.querySelector('.map-lat').value = resultLat;
                document.querySelector('.map-lng').value = resultLon;
            }
        }


        document.querySelector('.openstreetmap-search-block .search-submit').addEventListener('click',function (event) {

            event.preventDefault();

            let searchVal = document.querySelector('.search-input').value;
            let resultSearchHtml = '';
            let apiGeocoder = 'https://nominatim.openstreetmap.org/search.php?format=json&q='+searchVal;
            console.log(apiGeocoder);
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

                            console.log(data);
                            if(data.length > 0){
                                for( var dataItem of data ){
                                    resultSearchHtml += '<div class="'+ classItemResult +'" data-lat="'+ dataItem.lat +'" data-lon="'+ dataItem.lon +'" data->'+ dataItem.display_name +'</div>';
                                }
                            }else{
                                resultSearchHtml = 'NO result'
                            }


                            document.querySelector('.openstreetmap-search-result').innerHTML = resultSearchHtml;

                        });
                    }
                )
                .catch(function(err) {
                    console.log('Fetch Error :-S', err);
                    return false;
                });
        })


    });

}
