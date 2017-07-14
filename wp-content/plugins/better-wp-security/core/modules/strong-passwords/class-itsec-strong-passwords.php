<?php

final class ITSEC_Strong_Passwords {
	public function __construct() {
		add_action( 'user_profile_update_errors', array( $this, 'filter_user_profile_update_errors' ), 0, 3 );
		add_action( 'validate_password_reset', array( $this, 'validate_password_reset' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'add_scripts' ) );
	}

	/**
	 * Enqueue script to add measured password strength to the form submission data.
	 *
	 * @return void
	 */
	public function add_scripts() {
		$module_path = ITSEC_Lib::get_module_path( __FILE__ );

		wp_enqueue_script( 'itsec_strong_passwords', $module_path . 'js/script.js', array( 'jquery' ), ITSEC_Core::get_plugin_build() );
	}

	/**
	 * Handle submission of a form to create or edit a user.
	 *
	 * @param WP_Error $errors WP_Error object.
	 * @param bool     $update Whether this is a user update.
	 * @param stdClass $user   User object.
	 *
	 * @return WP_Error
	 */
	public function filter_user_profile_update_errors( $errors, $update, $user ) {
		if ( $errors->get_error_data( 'pass' ) ) {
			// An error regarding the password was already found.
			return $errors;
		}

		$password_strength = false;

		if ( ! isset( $user->user_pass ) && $update ) {
			// The password was not changed, but an update is occurring. Test to see if we need to prompt for a password change.
			// This also handles the case where a user's role is being changed to one that requires strong password enforcement.

			$password_strength = get_user_meta( $user->ID, 'itsec-password-strength', true );

			if ( false === $password_strength || '' === $password_strength || ! in_array( $password_strength, range( 0, 4 ) )  ) {
				// Not enough data to determine whether a change of password is required.
				return $errors;
			}
		}

		$wp_roles = wp_roles();
		$user_caps = array();

		if ( $update ) {
			// We're updating the user, make sure that we keep a list of additional caps that may have been added to the user.
			// Since the $user_obj->caps array contains both roles and caps added to the user, we have to check whether the cap is a role or cap.

			$user_obj = get_user_by( 'id', $user->ID );

			if ( isset( $user_obj->caps ) && is_array( $user_obj->caps ) ) {
				foreach ( array_keys( $user_obj->caps ) as $cap ) {
					if ( ! $wp_roles->is_role( $cap ) ) {
						$user_caps[] = $cap;
					}
				}
			}
		}

		if ( isset( $user->role ) ) {
			// A user other than the current user is being created or updated, $user->role contains the role selected on the form.
			$role = $user->role;
		} else if ( isset( $user_obj ) ) {
			// The current user is being updated, this makes the logic simple as we can simply use $user_obj->allcaps to get a complete list of the user's caps.
			$caps = array_keys( $user_obj->allcaps );
		} else {
			// Something strange is going on, as a last-ditch effort, use the default role as done by wp_insert_user() when the role isn't provided.
			$role = get_option( 'default_role' );
		}

		if ( ! isset( $caps ) ) {
			// A user other than the current user is being created or updated.

			$the_role = $wp_roles->get_role( $role );

			// Merge the role's list of caps with the caps added to the user.
			$caps = array_merge( $user_caps, array_keys( $the_role->capabilities ) );

			// Ensure that there aren't any duplicate caps.
			$caps = array_unique( $caps );
		}

		if ( $this->fails_enforcement( $user, $caps, $password_strength ) ) {
			if ( $update ) {
				$errors->add( 'pass', wp_kses( __( '<strong>Error</strong>: Due to site rules, a strong password is required. Please choose a new password that rates as <strong>Strong</strong> on the meter. The user changes have not been saved.', 'better-wp-security' ), array( 'strong' => array() ) ) );
			} else {
				$errors->add( 'pass', wp_kses( __( '<strong>Error</strong>: Due to site rules, a strong password is required. Please choose a new password that rates as <strong>Strong</strong> on the meter. The user has not been created.', 'better-wp-security' ), array( 'strong' => array() ) ) );
			}
		}

		return $errors;
	}

	/**
	 * Handle password reset requests.
	 *
	 * @param WP_Error $errors WP_Error object.
	 * @param WP_User  $user   WP_User object.
	 *
	 * @return WP_Error
	 */
	public function validate_password_reset( $errors, $user ) {
		if ( ! isset( $_POST['pass1'] ) ) {
			// The validate_password_reset action fires when first rendering the reset page and when handling the form
			// submissions. Since the pass1 data is missing, this must be the initial page render. So, we don't need to
			// do anything yet.

			return;
		}

		$caps = array_keys( $user->allcaps );

		if ( $this->fails_enforcement( $user, $caps ) ) {
			$errors->add( 'pass', wp_kses( __( '<strong>Error</strong>: Due to site rules, a strong password is required. Please choose a new password that rates as <strong>Strong</strong> on the meter. The password has not been updated.', 'better-wp-security' ), array( 'strong' => array() ) ) );
		}
	}

	/**
	 * Determine if the user requires enforcement and if it fails that enforcement.
	 *
	 * @param WP_User|object $user              Requires either a valid WP_User object or an object that has the following members:
	 *                                          user_login, first_name, last_name, nickname, display_name, user_email, user_url, and
	 *                                          description. A member of user_pass is required if $password_strength is false.
	 * @param int|boolean    $password_strength [optional] An integer value representing the password strength, if known, or false.
	 *                                          Defaults to false.
	 *
	 * @return boolean True if the user requires enforcement and has a password weaker than strong. False otherwise.
	 */
	private function fails_enforcement( $user, $caps, $password_strength = false ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );
		$role = ITSEC_Lib_Canonical_Roles::get_role_from_caps( $caps );
		$min_role = ITSEC_Modules::get_setting( 'strong-passwords', 'role' );

		if ( ! ITSEC_Lib_Canonical_Roles::is_canonical_role_at_least( $min_role, $role ) ) {
			return false;
		}

		if ( false === $password_strength ) {
			if ( ! empty( $_POST['password_strength'] ) && 'strong' !== $_POST['password_strength'] ) {
				// We want to validate the password strength if the form data says that the password is strong since we want
				// to protect against spoofing. If the form data says that the password isn't strong, believe it.

				$password_strength = 1;
			} else {
				// The form data does not indicate a password strength or the data claimed that the password is strong,
				// which is a claim that must be validated. Use the zxcvbn library to find the password strength score.

				$penalty_strings = array(
					$user->user_login,
					$user->first_name,
					$user->last_name,
					$user->nickname,
					$user->display_name,
					$user->user_email,
					$user->user_url,
					$user->description,
					get_site_option( 'admin_email' ),
				);

				$results = ITSEC_Lib::get_password_strength_results( $user->user_pass, $penalty_strings );
				$password_strength = $results->score;
			}
		}

		if ( $password_strength < 4 ) {
			return true;
		}

		return false;
	}
}

new ITSEC_Strong_Passwords();
