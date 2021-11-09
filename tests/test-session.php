<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 * Simple session tests
 */
class Test_Session extends WP_UnitTestCase {
	/**
	 * @var NSESS_Session
	 */
	private NSESS_Session $session;

	/**
	 * @var ReflectionClass
	 */
	private ReflectionClass $reflection;

	public function setUp() {
		$this->session    = new NSESS_Session();
		$this->reflection = new ReflectionClass( $this->session );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_cookie_value() {
		$generate = $this->reflection->getMethod( 'generate_cookie_value' );
		$generate->setAccessible( true );

		$verify = $this->reflection->getMethod( 'verify_cookie_value' );
		$verify->setAccessible( true );

		$name = nsess_cookie_name();
		$hash = $generate->invoke( $this->session, 'session-test-#1023' );

		$_COOKIE[ $name ] = urlencode( $hash );
		$this->assertTrue( $verify->invoke( $this->session ) );
		unset( $_COOKIE[ $name ] );
	}

	/**
	 * @dataProvider provider_shake_storage
	 *
	 * @throws ReflectionException
	 */
	public function test_shake_storage( $expected, $storage, $compare ) {
		$session = $this->reflection->getProperty( 'session_storage' );
		$expiry  = $this->reflection->getProperty( 'expiry_storage' );
		$shake   = $this->reflection->getMethod( 'shake_storage' );

		$session->setAccessible( true );
		$expiry->setAccessible( true );
		$shake->setAccessible( true );

		$session->setValue( $this->session, $storage['session'] );
		$expiry->setValue( $this->session, $storage['expiry'] );
		$actual = $shake->invoke( $this->session );

		$this->assertEquals( $expected, $actual );
		$this->assertEqualSets( $compare['session'], $session->getValue( $this->session ) );
		$this->assertEqualSets( $compare['expiry'], $expiry->getValue( $this->session ) );
	}

	public function provider_shake_storage(): array {
		$future = time() + 3600;
		$past   = time() - 3600;

		return [
			// Empty storage will return 0.
			[
				'expected' => 0,
				'storage'  => [
					'session' => [],
					'expiry'  => [],
				],
				'compare'  => [
					'session' => [],
					'expiry'  => [],
				],
			],
			// 'foo' will be removed, while 'bar' will be kept.
			[
				'expected' => $future,
				'storage'  => [
					'session' => [
						'foo' => '1',
						'bar' => '2',
					],
					'expiry'  => [
						'foo' => $past,
						'bar' => $future,
					],
				],
				'compare'  => [
					'session' => [
						'bar' => '2',
					],
					'expiry'  => [
						'bar' => $future,
					],
				],
			],
			// All removed.
			[
				'expected' => 0,
				'storage'  => [
					'session' => [
						'foo' => '1',
						'bar' => '2',
					],
					'expiry'  => [
						'foo' => $past,
						'bar' => $past - 100,
					],
				],
				'compare'  => [
					'session' => [],
					'expiry'  => [],
				],
			],
			// All saved.
			[
				'expected' => $future + 100,
				'storage'  => [
					'session' => [
						'foo' => '1',
						'bar' => '2',
					],
					'expiry'  => [
						'foo' => $future,
						'bar' => $future + 100,
					],
				],
				'compare'  => [
					'session' => [
						'foo' => '1',
						'bar' => '2',
					],
					'expiry'  => [
						'foo' => $future,
						'bar' => $future + 100,
					],
				],
			],
		];
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_get_set_fresh() {
		// Check if default works.
		$val = $this->session->get( 'foo', 'alt' );
		$this->assertEquals( 'alt', $val );

		// Check if set/get works.
		$this->session->set( 'foo', 1 );
		$this->assertSame( 1, $this->session->get( 'foo' ) );

		$this->session->set( 'foo', 2 );
		$this->assertSame( 2, $this->session->get( 'foo' ) );

		// Check if delete works
		$this->session->set( 'foo', null );
		$this->assertNull( $this->session->get( 'foo' ) );

		// Check if non-exist key expiration
		$this->assertEquals( 0, $this->session->get_expiration( 'foo' ) );

		$this->session->set( 'foo', '1' );
		$expiration = $this->session->get_expiration( 'foo' );

		$prop = $this->reflection->getProperty( 'session_id' );
		$prop->setAccessible( true );
		$session_id = $prop->getValue( $this->session );

		$this->session->save_session();
		$storage = get_transient( 'nsess_' . $session_id );
		$this->assertEquals( '1', $storage['session']['foo'] );
		$this->assertEquals( $expiration, $storage['expiry']['foo'] );
	}
}
