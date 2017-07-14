<?php

add_action( 'rest_api_init', 'wpcf7_rest_api_init' );

function wpcf7_rest_api_init() {
	$namespace = 'contact-form-7/v1';

	register_rest_route( $namespace,
		'/contact-forms',
		array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => 'wpcf7_rest_get_contact_forms',
			),
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => 'wpcf7_rest_create_contact_form',
			),
		)
	);

	register_rest_route( $namespace,
		'/contact-forms/(?P<id>\d+)',
		array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => 'wpcf7_rest_get_contact_form',
			),
			array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => 'wpcf7_rest_update_contact_form',
			),
			array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => 'wpcf7_rest_delete_contact_form',
			),
		)
	);
}

function wpcf7_rest_get_contact_forms( WP_REST_Request $request ) {
	if ( ! current_user_can( 'wpcf7_read_contact_forms' ) ) {
		return new WP_Error( 'wpcf7_forbidden',
			__( "You are not allowed to access contact forms.", 'contact-form-7' ),
			array( 'status' => 403 ) );
	}

	$args = array();

	$per_page = $request->get_param( 'per_page' );

	if ( null !== $per_page ) {
		$args['posts_per_page'] = (int) $per_page;
	}

	$offset = $request->get_param( 'offset' );

	if ( null !== $offset ) {
		$args['offset'] = (int) $offset;
	}

	$order = $request->get_param( 'order' );

	if ( null !== $order ) {
		$args['order'] = (string) $order;
	}

	$orderby = $request->get_param( 'orderby' );

	if ( null !== $orderby ) {
		$args['orderby'] = (string) $orderby;
	}

	$search = $request->get_param( 'search' );

	if ( null !== $search ) {
		$args['s'] = (string) $search;
	}

	$items = WPCF7_ContactForm::find( $args );

	$response = array();

	foreach ( $items as $item ) {
		$response[] = array(
			'id' => $item->id(),
			'slug' => $item->name(),
			'title' => $item->title(),
			'locale' => $item->locale(),
		);
	}

	return rest_ensure_response( $response );
}

function wpcf7_rest_create_contact_form( WP_REST_Request $request ) {
	$id = (int) $request->get_param( 'id' );

	if ( $id ) {
		return new WP_Error( 'wpcf7_post_exists',
			__( "Cannot create existing contact form.", 'contact-form-7' ),
			array( 'status' => 400 ) );
	}

	if ( ! current_user_can( 'wpcf7_edit_contact_forms' ) ) {
		return new WP_Error( 'wpcf7_forbidden',
			__( "You are not allowed to create a contact form.", 'contact-form-7' ),
			array( 'status' => 403 ) );
	}

	$args = $request->get_params();
	$args['id'] = -1; // Create
	$context = $request->get_param( 'context' );
	$item = wpcf7_save_contact_form( $args, $context );

	if ( ! $item ) {
		return new WP_Error( 'wpcf7_cannot_save',
			__( "There was an error saving the contact form.", 'contact-form-7' ),
			array( 'status' => 500 ) );
	}

	$response = array(
		'id' => $item->id(),
		'slug' => $item->name(),
		'title' => $item->title(),
		'locale' => $item->locale(),
		'properties' => $item->get_properties(),
		'config_errors' => array(),
	);

	if ( wpcf7_validate_configuration() ) {
		$config_validator = new WPCF7_ConfigValidator( $item );
		$config_validator->validate();

		$response['config_errors'] = $config_validator->collect_error_messages();

		if ( 'save' == $context ) {
			$config_validator->save();
		}
	}

	return rest_ensure_response( $response );
}

function wpcf7_rest_get_contact_form( WP_REST_Request $request ) {
	$id = (int) $request->get_param( 'id' );
	$item = wpcf7_contact_form( $id );

	if ( ! $item ) {
		return new WP_Error( 'wpcf7_not_found',
			__( "The requested contact form was not found.", 'contact-form-7' ),
			array( 'status' => 404 ) );
	}

	if ( ! current_user_can( 'wpcf7_edit_contact_form', $id ) ) {
		return new WP_Error( 'wpcf7_forbidden',
			__( "You are not allowed to access the requested contact form.", 'contact-form-7' ),
			array( 'status' => 403 ) );
	}

	$response = array(
		'id' => $item->id(),
		'slug' => $item->name(),
		'title' => $item->title(),
		'locale' => $item->locale(),
		'properties' => $item->get_properties(),
	);

	return rest_ensure_response( $response );
}

function wpcf7_rest_update_contact_form( WP_REST_Request $request ) {
	$id = (int) $request->get_param( 'id' );
	$item = wpcf7_contact_form( $id );

	if ( ! $item ) {
		return new WP_Error( 'wpcf7_not_found',
			__( "The requested contact form was not found.", 'contact-form-7' ),
			array( 'status' => 404 ) );
	}

	if ( ! current_user_can( 'wpcf7_edit_contact_form', $id ) ) {
		return new WP_Error( 'wpcf7_forbidden',
			__( "You are not allowed to access the requested contact form.", 'contact-form-7' ),
			array( 'status' => 403 ) );
	}

	$args = $request->get_params();
	$context = $request->get_param( 'context' );
	$item = wpcf7_save_contact_form( $args, $context );

	if ( ! $item ) {
		return new WP_Error( 'wpcf7_cannot_save',
			__( "There was an error saving the contact form.", 'contact-form-7' ),
			array( 'status' => 500 ) );
	}

	$response = array(
		'id' => $item->id(),
		'slug' => $item->name(),
		'title' => $item->title(),
		'locale' => $item->locale(),
		'properties' => $item->get_properties(),
		'config_errors' => array(),
	);

	if ( wpcf7_validate_configuration() ) {
		$config_validator = new WPCF7_ConfigValidator( $item );
		$config_validator->validate();

		$response['config_errors'] = $config_validator->collect_error_messages();

		if ( 'save' == $context ) {
			$config_validator->save();
		}
	}

	return rest_ensure_response( $response );
}

function wpcf7_rest_delete_contact_form( WP_REST_Request $request ) {
	$id = (int) $request->get_param( 'id' );
	$item = wpcf7_contact_form( $id );

	if ( ! $item ) {
		return new WP_Error( 'wpcf7_not_found',
			__( "The requested contact form was not found.", 'contact-form-7' ),
			array( 'status' => 404 ) );
	}

	if ( ! current_user_can( 'wpcf7_delete_contact_form', $id ) ) {
		return new WP_Error( 'wpcf7_forbidden',
			__( "You are not allowed to access the requested contact form.", 'contact-form-7' ),
			array( 'status' => 403 ) );
	}

	$result = $item->delete();

	if ( ! $result ) {
		return new WP_Error( 'wpcf7_cannot_delete',
			__( "There was an error deleting the contact form.", 'contact-form-7' ),
			array( 'status' => 500 ) );
	}

	$response = array( 'deleted' => true );

	return rest_ensure_response( $response );
}
