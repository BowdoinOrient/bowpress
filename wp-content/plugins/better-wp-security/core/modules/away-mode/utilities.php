<?php

final class ITSEC_Away_Mode_Utilities {
	public static function has_active_file() {
		if ( @file_exists( self::get_active_file_name() ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function create_active_file() {
		if ( self::has_active_file() ) {
			return true;
		}

		$file = self::get_active_file_name();

		$result = @file_put_contents( $file, 'true' );

		if ( false === $result ) {
			return false;
		} else {
			return true;
		}
	}

	public static function delete_active_file() {
		if ( ! self::has_active_file() ) {
			return true;
		}

		$file = self::get_active_file_name();

		return @unlink( $file );
	}

	public static function get_active_file_name() {
		$file_name = apply_filters( 'itsec_filer_away_mode_active_file', ITSEC_Core::get_storage_dir() . '/itsec_away.confg' );

		return $file_name;
	}

	public static function is_current_timestamp_active( $start, $end, $include_details = false ) {
		$now = ITSEC_Core::get_current_time_gmt();

		$active = false;

		if ( $start <= $now && $now <= $end ) {
			$active = true;
		}

		if ( ! $include_details ) {
			return $active;
		}


		if ( $start > $end ) {
			$remaining = false;
			$next = false;
			$length = false;

			/* translators: 1: start timestamp, 2: end timestamp */
			$error = new WP_Error( 'itsec-away-mode-is-current-timestamp-in-range-start-after-end', sprintf( __( 'The supplied data is invalid. The supplied start (%1$s) is after the supplied end (%2$s).', 'better-wp-security' ), $start, $end ) );
		} else {
			$remaining = $end - $now;
			$next = $start - $now;
			$length = $end - $start;
			$error = false;

			if ( $now < $start ) {
				$remaining = false;
			}

			if ( $next < 0 ) {
				$next = false;
			}
		}

		return compact( 'active', 'remaining', 'next', 'length', 'error' );
	}

	public static function is_current_time_active( $start, $end, $include_details = false ) {
		$current_time = ITSEC_Core::get_current_time();
		$now = $current_time - strtotime( date( 'Y-m-d', $current_time ) );

		$active = false;

		if ( $start <= $end ) {
			if ( $start <= $now && $now <= $end ) {
				$active = true;
			}
		} else {
			if ( $start <= $now || $now <= $end ) {
				$active = true;
			}
		}

		if ( ! $include_details ) {
			return $active;
		}


		$remaining = $end - $now;
		$next = $start - $now;
		$length = $end - $start;

		if ( $active && $remaining < 0 ) {
			$remaining += DAY_IN_SECONDS;
		} else if ( ! $active && $remaining >= 0 ) {
			$remaining -= DAY_IN_SECONDS;
		}

		if ( $next < 0 ) {
			$next += DAY_IN_SECONDS;
		}

		if ( $length < 0 ) {
			$length += DAY_IN_SECONDS;
		}


		return compact( 'active', 'remaining', 'next', 'length' );
	}
}
