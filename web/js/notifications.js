$('.push-notifications').on('click', '.push-notification-inner-close' , function (e) {
    e.preventDefault();
    removePushNotification( $(this).parents('.push-notification-item').attr('id') );
});




function addPushNotification(type, message, time) {

    var pushItemHtml = $('.push-notification-item-default').html();
    var newPushId = 'pushItem-' + $('.push-notification-item').length ;

    $('.push-notifications').append('<div id="'+ newPushId +'" class="push-notification-item '+ type +' " >'+ pushItemHtml +'</div>');
    $('#'+newPushId+' .push-notification-inner-message').html(message);
    $('#'+newPushId).show('slow');
    if( type == 'error'){
        time = 10000;
    }
    if( time ){
        setTimeout( function () {
            removePushNotification(newPushId);
        }, time );
    }

}

function removePushNotification(id){
    $('#'+id).hide('slow', function () {
        $('#'+id).remove();
    });
}


function startLoader() {
    $('.ajax-loader').addClass('active');
}
function endLoader() {
    $('.ajax-loader').removeClass('active');
}


/*addPushNotification('', 'Hi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error message!', 3000 );

addPushNotification('success', 'Hi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error message!',3200 );

addPushNotification('error', 'Hi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error message!',3500 );

addPushNotification('', 'Hi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error messageHi! Its error! And its error message!',3600 );
*/
