# 나란 세션 플러그인

이 플러그인은 워드프레스에 쿠키 기반의 세션 기능을 제공합니다.

## 설치하기

보통의 플러그인과 마찬가지로 `wp-content/plugins` 디렉토리에 이 플러그인을 설치하시고, 관리자 > 플러그인에서 활성화하세요.

단, 이 플러그인은 일반적인 사용자를 위한 것이 아닌, 개발자의 편의를 위한 용도로 작성되었습니다.

## 사용하기

각 사용자의 식별은 쿠키에 설정된 식별자 값을 통해 이뤄집니다.

### nsess_init()

세션을 시작하는 함수입니다.

### nsess_get( string $key, $default = null )

세션에 저장된 값을 가져오는 함수입니다.

* $key: 문자열로, 세션 내 값의 식별자입니다.
* $default: 세션에 해당 식별자로 저장된 값이 없을 때 대체할 값을 지정할 수 있습니다.

### nsess_set( string $key, $value )

세션에 값을 저장하는 함수입니다.

* $key: 문자열로, 세션에 설정할 값의 식별자입니다.
* $value: serialize 가능한 어떤 값이든 가능합니다. 단, null을 입력하면 해당 키를 세션에서 제거하는 것과 동일합니다.

### nsess_remove( string $key )

세션의 값을 삭제하는 함수입니다.
`ness_remove( 'foo' )`와 `nsess_set( 'foo', null )` 동일한 동작을 합니다.

* $key: 문자열로, 삭제할 세션 값의 식별자입니다.

### nsess_reset()

세션을 완전히 초기화합니다.

## 기타 설정

### 상수

wp-config.php 에 미리 설정할 수 있습니다.

* `NSESS_COOKIE_NAME`: 쿠키 이름을 설정할 수 있습니다. 기본값은 'nsess' 입니다.
* `NSESS_TIMEOUT`: 세션 쿠키의 만료 시간을 설정할 수 있습니다. 초 단위로 입력 가능하며 기본값은 86400 (1일)입니다. 0이거나 음수이면 기본값인 86400으로 간주됩니다.
* `NSESS_COOKIEPATH`: 세션 쿠키의 경로를 지정합니다. 기본은 공백이며, 이 경우 워드프레스가 지정한 `COOKIEPATH` 상수값을 사용합니다.
* `NSESS_COOKIE_DOMAIN`: 세션 쿠키의 도메인을 지정합니다. 기본은 공백이며, 이 경우 워드프레스가 지정한 `COOKIE_DOMAIN` 상수값을 사용합니다.
* `NSESS_SECURE`: 세션 쿠키를 https 에서만 사용할지 말지를 결정합니다. 기본은 공백이며, 이때는 https 접속에만 사용 가능한 쿠키를 생성합니다. 
* `NSESS_HTTP_ONLY`: 스크립트에서 차단 가능한 쿠키를 사용할지를 결정합니다. 참일 경우 스크립트에서 접근 불가합니다. 기본은 true 입니다. 

`NSESS_SECURE`, `NSESS_HTTP_ONLY`는 불리언 값을 입력받지만, 문자열이나 정수로도 입력 가능합니다.
* true와 동치: 'yes', 'on', '1', 1
* false와 동치: 'no', 'off', '0', 0

아래는 기본값의 예시입니다.
```
define( 'NSESS_COOKIE_NAME', 'nsess' );
define( 'NSESS_TIMEOUT', 86400 );
define( 'NSESS_COOKIEPATH', '' );
define( 'NSESS_COOKIE_DOMAIN', '' );
define( 'NSESS_SECURE', '' );
define( 'NSESS_HTTP_ONLY', 'yes' );
```
