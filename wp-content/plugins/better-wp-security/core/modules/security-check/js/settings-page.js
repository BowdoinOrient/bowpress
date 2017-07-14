jQuery( document ).ready( function ( $ ) {
	var $container = $( '#itsec-module-card-security-check' )

	$container.on( 'click', '#itsec-security-check-secure_site', function( e ) {
		e.preventDefault();

		$( '#itsec-security-check-secure_site' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.attr( 'value', itsec_security_check_settings.securing_site )
			.prop( 'disabled', true );

		$( '#itsec-security-check-details-container' ).html( '' );

		var data = {
			'method': 'secure-site'
		};

		itsecSettingsPage.sendModuleAJAXRequest( 'security-check', data, function( results ) {
			$( '#itsec-security-check-secure_site' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.attr( 'value', itsec_security_check_settings.rerun_secure_site )
				.prop( 'disabled', false );

			$( '#itsec-security-check-details-container' ).html( results.response );
		} );
	} );

	$container.on( 'click', '#itsec-security-check-enable_network_brute_force', function( e ) {
		e.preventDefault();

		var original_button_name = $( '#itsec-security-check-enable_network_brute_force' ).attr( 'value' );

		$( '#itsec-security-check-enable_network_brute_force' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.attr( 'value', itsec_security_check_settings.activating_network_brute_force )
			.prop( 'disabled', true );

		var data = {
			'method':        'activate-network-brute-force',
			'email':         $( '#itsec-security-check-email' ).attr( 'value' ),
			'updates_optin': $( '#itsec-security-check-updates_optin option:selected' ).text()
		};

		itsecSettingsPage.sendModuleAJAXRequest( 'security-check', data, function( results ) {
			$( '#itsec-security-check-enable_network_brute_force' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.attr( 'value', original_button_name )
				.prop( 'disabled', false );

			$( '#itsec-security-check-network-brute-force-errors' ).html( '' );
			var $container = $( '#itsec-module-card-security-check #itsec-security-check-network-brute-force-container' );

			if ( results.errors && results.errors.length > 0 ) {
				$container
					.removeClass( 'itsec-security-check-container-incomplete' )
					.removeClass( 'itsec-security-check-container-complete' )
					.addClass( 'itsec-security-check-container-error' );

				$.each( results.errors, function( index, error ) {
					$( '#itsec-security-check-network-brute-force-errors' ).append( '<div class="error inline"><p><strong>' + error + '</strong></p></div>' );
				} );
			} else {
				$container
					.removeClass( 'itsec-security-check-container-incomplete' )
					.removeClass( 'itsec-security-check-container-error' )
					.addClass( 'itsec-security-check-container-complete' );

				$container.html( results.response );
				$( '#itsec-notice-network-brute-force' ).hide();
			}
		} );
	} );
} );
