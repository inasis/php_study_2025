#  의존성 주입

소프트웨어 개발을 하다 보면 누구나 "코드를 수정했더니 예상치 못한 다른 곳에서 오류가 발생했다"거나, "기능 하나를 테스트하고 싶은데 너무 많은 다른 코드들과 얽혀있어 테스트조차 하기 힘든" 경험을 하게 됩니다. 이런 문제들은 대부분 객체들이 서로 너무 단단하게 얽혀있기 때문에 발생합니다.

의존성 주입은 이런 문제를 해결하기 위한 매우 강력하고 우아한 설계 패턴입니다. 단순히 코딩 기술 하나를 배우는 것이 아니라, 유연하고, 테스트하기 쉽고, 장기적으로 유지보수하기 좋은 코드를 작성하는 '생각의 방식'을 배우는 것입니다. 지금부터 DI가 무엇인지, 왜 필요한지, 그리고 어떻게 사용하는지 차근차근 알아보겠습니다.

## 1. 손님이 직접 요리하는 식당

의존성 주입을 이해하기 위해, 먼저 의존성 주입이 없는 코드가 어떤 문제를 일으키는지 살펴보겠습니다. 식탁에 앉아 셰프의 요리를 기다리는 손님을 통해 이 상황을 이해해 보도록 하겠습니다.

제일 먼저 설명할 원칙은 '제어의 역전(Inversion of Control, IoC)'입니다. 다시 말해 객체(손님)가 사용할 의존성(요리)을 직접 만드는 것이 아니라, 그 제어권을 외부(셰프)에 넘기는 것입니다. 의존성 주입은 바로 이 원칙을 구현하는 대표적인 방법입니다.

### 1.1. 나쁜 설계: 손님이 직접 주방으로 들어가 요리합니다

아래 코드는 `Guest`(손님) 클래스가 `new Steak()` 코드를 통해 직접 요리를 만드는 상황입니다.

요리 'Steak' 클래스를 정의합니다.

```
class Steak
{
    public function taste(): string
    {
        return "육즙이 풍부한 스테이크";
    }
}
```

그러나 다음 상황에서 문제가 발생합니다.

```
class Guest
{
    private Steak $meal;

    public function __construct()
    {
        $this->meal = new Steak();
    }

    public function enjoyMeal(): void
    {
        echo "손님이 식사를 합니다: " . $this->meal->taste() . "\n";
    }
}

$guest = new Guest();
$guest->enjoyMeal();
```

손님이 직접 'Steak' 요리를 결정하고 조리합니다. 이것을 '강한 결합(Tight Coupling)'이라고 부릅니다.

#### 코드의 근본적인 문제점

1. **지나치게 많은 책임**
   `Guest` 클래스의 원래 책임은 '식사를 즐기는 것'입니다. 하지만 생성자의 `new Steak()` 때문에 '어떤 요리를 만들지 결정하고, 그 요리를 직접 조리하는' 책임까지 떠안게 되었습니다. 손님이 주방 일까지 신경 쓰는 이상한 식당이 된 것입니다.

2. **유연성 부족**
   만약 이 손님이 내일 파스타를 먹고 싶다면 어떻게 해야 할까요? `Guest` 클래스 내부의 `new Steak()`를 `new Pasta()`로 직접 수정해야 합니다. 메뉴가 바뀔 때마다 `Guest` 클래스를 찾아와 직접 수정을 가해야 하는 끔찍한 상황입니다.

3. **테스트의 어려움**
   `Guest` 클래스의 `enjoyMeal` 기능만 따로 테스트하고 싶다고 가정해 봅시다. 하지만 이 클래스는 `Steak` 클래스가 없으면 아예 동작하지 않습니다. 테스트를 위해 항상 '진짜 스테이크'를 준비해야 합니다. '가짜 샐러드'를 주면서 식사를 잘하는지 테스트해 볼 수가 없습니다.

### 1.2. 좋은 설계: 셰프가 요리를 제공하는 식당

이제 의존성 주입을 적용하여 이 문제를 해결해 보겠습니다. 핵심은 '역할의 분리'입니다. 손님은 식사만 하고, 요리는 셰프가 합니다.

```php
interface MealInterface
{
    public function taste(): string;
}
```

 '식사' 라는 행동에 대한 인터페이스를 정의합니다. 모든 요리는 식사에서 '맛'을 볼 수 있어야 합니다.

```php
class Steak implements MealInterface
{
    public function taste(): string
    {
        return "육즙이 풍부한 스테이크";
    }
}

class Pasta implements MealInterface
{
    public function taste(): string
    {
        return "알덴테로 익힌 크림 파스타";
    }
}
```

그리고 구현된 인터페이스에 따라 요리 `Steak` 와 `Pasta` 클래스를 정의합니다.

```php
class Guest
{
    public function __construct(
        private MealInterface $meal
    ) {}

    public function enjoyMeal(): void
    {
        // .
        echo "손님이 식사를 합니다: " . $this->meal->taste() . "\n";
    }
}
```

`Guest` 클래스는 이제 구체적인 요리가 아닌 '식사'라는 인터페이스에만 의존합니다. 생성자는 비어있는 접시와 같습니다. 손님은 그저 접시 위에 주어진 요리를 즐기기만 하면 됩니다. 셰프의 요리가 `Steak`인지 `Pasta`인지는 더 이상 중요하지 않습니다.

다시 애플리케이션의 시작점으로 돌아왔습니다. 이곳에서 셰프의 역할을 정의합니다.

```php
$todaysMeal = new Steak();
$guest1 = new Guest($todaysMeal);
$guest1->enjoyMeal();
```

셰프가 오늘의 메뉴로 `Steak`를 결정하고 요리한 다음 완성된 스테이크를 `Guest`에게 가져다 줍니다.

```php
$tomorrowsMeal = new Pasta();
$guest2 = new Guest($tomorrowsMeal);
$guest2->enjoyMeal();
```

다음 날, 셰프는 `Pasta`를 요리하기로 합니다. `Guest` 클래스를 단 한 줄도 변경하지 않고 메뉴를 바꿨습니다.

#### 무엇이 바뀌었고, 어떤 점이 좋아졌을까요?

1. **느슨한 결합(Loose Coupling)**
   `Guest` 클래스는 더 이상 `Steak`나 `Pasta`의 존재를 모릅니다. 오직 `MealInterface`라는 '메뉴판의 약속'만 알 뿐입니다. 덕분에 `Lobster`나 `Pizza`같은 새로운 메뉴가 추가되어도 `Guest` 클래스는 전혀 영향을 받지 않습니다.

2. **명확해진 책임**
   `Guest`는 '식사'라는 자신의 역할에만 충실하고, '요리 생성'이라는 책임은 외부 세계의 '셰프'에게 완전히 위임했습니다. 이것이 바로 **제어의 역전(IoC)** 입니다.

3. **쉬워진 테스트**
   이제 `Guest`를 테스트하기 매우 쉬워졌습니다. `MealInterface`를 구현하여 테스트를 위한`MockSalad` 객체를 만들어 손님에게 주입하면, `Guest` 클래스의 기능을 독립적으로 완벽하게 테스트할 수 있습니다.

## 2. 조립식 PC 맞추기

객체지향 프로그래밍에서 유연하고 재사용 가능한 코드를 만드는 일은 마치 **조립식 PC**를 만드는 것과 같습니다. 수많은 부품이 존재하지만, 정해진 규격(인터페이스)에 맞춰 조립하면 어떤 부품을 쓰든 PC(객체)는 문제없이 작동하죠. 이 개념을 중심으로 **의존성 주입**과, **직접 클래스 생성**, 그리고 **서비스 로케이터**라는 세 가지 설계 패턴의 차이를 알아보겠습니다.

### 2.1. 의존성 주입: 전문가에게 PC 조립을 맡기는 일

의존성 주입은 PC 조립 전문가에게 부품 목록만 전달하고, 조립은 전문가에게 맡기는 방식입니다. 당신이 "CPU는 AMD, 그래픽카드는 NVIDIA 최신 모델로 맞춰주세요"라고 요구하면, 전문가는 부품들의 호환성(인터페이스)을 완벽하게 이해하고 최적의 조합으로 PC를 완성해 당신에게 건넵니다.

`CustomPc` 클래스는 마치 당신의 요구사항을 담은 설계도와 같습니다. 이 클래스는 "PC를 만들려면 CPU와 GPU가 필요하다"고 '선언'만 할 뿐, 어떤 부품을 쓸지는 직접 결정하지 않습니다. 대신, 생성자를 통해 외부(전문가)에서 이미 조립된 부품을 받아 사용합니다.

```php
class CustomPc
{
    public function __construct(
        private CpuInterface $cpu,
        private GpuInterface $gpu
    ) {}

    public function boot(): void
    {
        echo "조립된 PC의 전원을 켭니다.\n";
        echo "- " . $this->cpu->processing() . "\n";
        echo "- " . $this->gpu->rendering() . "\n";
    }
}
```

실제로 조립이 이루어지는 곳은 애플리케이션의 시작점입니다. 여기서 '전문가'가 역할을 수행합니다.

```php
echo "요청사항: AMD CPU와 NVIDIA GPU로 PC를 조립해주세요.\n";
$selectedCpu = new AmdCpu();
$selectedGpu = new NvidiaGpu();

$myPc = new CustomPc($selectedCpu, $selectedGpu);
$myPc->boot();
```

만약 그래픽카드만 AMD로 업그레이드하고 싶다면 전문가에게 "그래픽카드만 바꿔주세요"라고 하면 됩니다. `CustomPc`는 손댈 필요 없이, 새로운 부품으로 교체해서 다시 조립만 하면 됩니다. 이렇게 의존성 주입은 부품(객체)을 자유롭게 교체할 수 있어 유연성과 재사용성을 극대화합니다.

```php
echo "\n요청사항 변경: AMD CPU와 AMD GPU로 바꿔주세요.\n";
$myNewPc = new CustomPc(new AmdCpu(), new AmdGpu());
$myNewPc->boot();
```

### 2.2. 직접 클래스 생성하기: 모든 것을 혼자서 조립하는 일

이 방식은 전문가 없이 모든 부품을 직접 알아보고, 구매하고, 조립하는 것과 같습니다. CPU 소켓 규격, RAM 클럭 호환성, 파워 용량 등 모든 것을 당신이 직접 책임져야 합니다. 과정이 번거롭고 실수의 가능성도 높죠.

`DiyPc` 클래스 내부에서 필요한 부품을 직접 `new AmdCpu()`와 같이 생성하고 있습니다. 이는 마치 PC 케이스 안에 부품을 납땜으로 고정하는 것과 같아, 특정 부품에 강하게 결합된 상태가 됩니다.

PHP

```
class DiyPc
{
    private CpuInterface $cpu;
    private GpuInterface $gpu;

    public function __construct()
    {
        echo "PC를 직접 조립합니다... 부품을 결정합니다.\n";
        $this->cpu = new AmdCpu();
        $this->gpu = new NvidiaGpu();
    }
}
```

이제 이 PC는 `AmdCpu`와 `NvidiaGpu`가 아니면 안 되는 운명이 됩니다. 만약 CPU를 Intel로 바꾸고 싶다면, `DiyPc` 클래스의 코드를 직접 열어 `new AmdCpu()` 부분을 수정해야 합니다. 부품 하나를 바꾸기 위해 클래스 코드를 수정해야 하니 유지보수가 어렵고, 테스트 역시 복잡해집니다.

### 2.3. 서비스 로케이터: 대형마트에서 완제품 PC를 구매하는 일

이 방식은 대형마트의 완제품 PC 코너에 가서 "게이밍 PC 주세요"라고 요청하는 것과 비슷합니다. 직원은 이미 정해진 모델만 가져다줄 수 있으며, 당신은 내부 부품을 마음대로 바꿀 수 없습니다. 더 큰 문제는, PC를 샀을 때 어떤 부품(의존성)이 들어 있는지 포장만 봐서는 전혀 알 수 없다는 점입니다.

`StoreBoughtPc` 클래스의 생성자는 어떤 부품도 없이 깔끔해 보이지만, 이는 숨겨진 의존성을 만들어냅니다.

```php
class StoreBoughtPc
{
    public function __construct()
    {
        echo "마트에서 완제품 PC를 구매했습니다.\n";
    }

    public function boot(): void
    {
        $cpu = PcPartStore::get('gaming_cpu');
        $gpu = PcPartStore::get('gaming_gpu');

        echo "완제품 PC의 전원을 켭니다.\n";
        echo "- " . $cpu->processing() . "\n";
        echo "- " . $gpu->rendering() . "\n";
    }
}
```

이 PC가 어떤 부품을 필요로 하는지 알려면 `boot()` 메서드 내부 코드를 직접 확인해야만 합니다. 이는 코드의 예측 가능성을 크게 떨어뜨립니다. 또한, 이 PC를 테스트하려면 항상 `PcPartStore`라는 전역 '대형마트'가 미리 준비되어 있어야만 하므로 테스트도 복잡해집니다. 이 때문에 서비스 로케이터는 대부분의 경우 피해야 할 안티패턴으로 여겨집니다.
## 3. DI 컨테이너: 당신의 전속 셰프이자 PC 조립 전문가

프로젝트가 커지면서 수십, 수백 개의 객체와 그들의 의존성을 손으로 직접 관리하는 것은 마치 거대한 식당에 필요한 모든 요리를 셰프가 혼자서 만드는 일과 같습니다. 이때 등장하는 것이 바로 DI 컨테이너(Container)입니다.

이는 의존성 주입 패턴을 자동화하는 강력한 도구입니다. 당신은 컨테이너에게 "이런 객체가 있고, 이 재료들로 이런 객체를 만들면 된다"고 조리법을 한 번만 알려주면 됩니다. 그 후로는 필요할 때마다 컨테이너에게 완성된 요리를 주문하면, 컨테이너가 알아서 모든 재료를 조합하여 완벽하게 작동하는 객체를 즉시 만들어줍니다.

### 3.1. DI 컨테이너를 활용한 PC 조립

이제 이 컨테이너에 PC를 조립하는 방법을 등록하고 사용해 보겠습니다. 이 과정은 마치 당신이 전문가에게 조립법을 한 번만 알려주고, 이후로는 모든 것을 맡기는 것과 동일합니다.

먼저, 당신의 전속 PC 조립 전문가 DI 컨테이너를 고용하겠습니다.

```php
class SimpleDiContainer
{
    private array $definitions = [];

    public function register(string $id, callable $factory): void
    {
        $this->definitions[$id] = $factory;
    }
  
    public function resolve(string $id): object
    {
        if (!isset($this->definitions[$id])) {
            throw new \Exception("'{ $id }'에 대한 정의가 없습니다.");
        }
  
        $factory = $this->definitions[$id];
        return $factory($this);
    }
}

$container = new SimpleDiContainer();
```

전문가에게 어떤 부품들을 사용할지 알려줍니다. 각 부품에는 `intelCpu`, `amdGpu`와 같이 이름표를 붙여줍니다.

```php
$container->register('intelCpu', fn() => new IntelCpu());
$container->register('amdCpu', fn() => new AmdCpu());
$container->register('nvidiaGpu', fn() => new NvidiaGpu());
$container->register('amdGpu', fn() => new AmdGpu());
```

이제 전문가에게 조립법을 알려줍니다. 당신은 "게이밍 PC를 만들려면, 인텔 CPU와 엔비디아 GPU를 사용해달라"고 한 번만 말해주면 됩니다.

```php
// 여기서는 Intel CPU와 NVIDIA GPU를 사용하도록 설정했습니다.
$container->register('myGamingPc', function(SimpleDiContainer $c) {
    return new CustomPc(
        $c->resolve('intelCpu'),
        $c->resolve('nvidiaGpu')
    );
});
```

이 조립법은 전문가에게 "PC는 모두 이 규격으로 맞춰달라"고 요청하는 것과 같습니다.

이제 완성된 PC가 필요할 때마다 전문가에게 "게이밍 PC 한 대 주세요"라고 간단히 요청하면 됩니다.

```php
$myPc = $container->resolve('myGamingPc');
$myPc->boot();
```

개발자는 더 이상 `CustomPc`가 어떤 부품들을 필요로 하는지 일일이 기억하거나 직접 조립할 필요가 없습니다.

만약 요청이 변경되어 AMD CPU와 AMD GPU로 구성된 새로운 PC를 원하는 경우에도, 전문가에게 새로운 조립법을 알려주기만 하면 됩니다.

```php
$container->register('myAmdPc', function(SimpleDiContainer $c) {
    return new CustomPc(
        $c->resolve('amdCpu'),
        $c->resolve('amdGpu')
    );
});

$myNewPc = $container->resolve('myAmdPc');
$myNewPc->boot();
```

### 3.2. 코드 분석

이 예시를 통해 DI 컨테이너의 핵심 역할을 명확하게 이해할 수 있습니다.

- **객체 생성 및 관리**
  `register` 메서드는 컨테이너에 "이런 부품을 이렇게 만들어라"고 지시하는 것과 같습니다. `IntelCpu`, `NvidiaGpu`, `CustomPc` 같은 객체들을 컨테이너가 책임지고 만들어 관리합니다.

- **의존성 해결**
  `myGamingPc`를 만들 때, 컨테이너는 정의된 조립법을 보고 필요한 부품(`intelCpu`, `nvidiaGpu`)을 스스로 찾아 `CustomPc`에 조립해 줍니다. 이는 마치 전문가가 당신의 요구사항을 듣고 알아서 부품을 골라 조립하는 것과 동일합니다.

- **유연성 및 재사용성**
  만약 PC 부품 구성(의존성)을 변경하고 싶다면, `CustomPc` 클래스 코드를 전혀 수정할 필요가 없습니다. 그저 컨테이너에 등록된 조립법만 수정하거나, 새로운 조립법을 추가하면 됩니다.

실제 DI 컨테이너들은 오토와이어링(Autowiring) 기능을 통해 이 과정을 더욱 자동화합니다. 클래스 생성자의 타입 힌트(`CpuInterface $cpu`, `GpuInterface $gpu`)만 보고도 컨테이너가 관리하는 부품들 중에서 해당 규격(인터페이스)을 만족하는 부품을 자동으로 찾아 주입해 줍니다. 이렇게 되면 개발자가 일일이 `register` 코드를 작성할 필요가 크게 줄어듭니다.

## 4. 그래서 DI를 언제, 왜 써야 하는가?

단순한 스크립트처럼 하나의 파일을 읽고 처리한 뒤 종료되는 아주 간단한 프로그램에서는 DI를 굳이 도입할 필요가 없습니다. 의존성이 거의 없고 구조가 단순한 경우에는 DI 컨테이너를 사용하지 않아도 됩니다.

그러나 애플리케이션이 단순하지 않고, 여러 객체가 서로 협력하여 동작하는 구조라면 DI를 도입하는 것이 매우 유익합니다. 특히 복잡성이 조금이라도 있는 시스템에서는 객체 간의 관계를 명확히 하고 유지보수를 쉽게 하기 위해 DI가 큰 도움이 됩니다.

또한, 단위 테스트가 중요한 프로젝트라면 DI는 선택이 아닌 필수입니다. 테스트 환경에서 객체를 쉽게 교체하거나 모의 객체를 주입할 수 있어 테스트의 효율성과 정확성이 높아집니다.

기능 확장이 자주 일어날 것으로 예상되거나, 개발·테스트·운영 등 다양한 환경에서 서로 다른 설정을 사용해야 하는 경우에도 DI는 매우 유연한 구조를 제공하여 환경에 따라 적절한 의존성을 손쉽게 주입할 수 있어 개발 생산성이 향상됩니다.

의존성 주입은 단순히 코드를 작성하는 기술을 넘어, 변경에 유연하고, 테스트하기 용이하며, 협업하기 좋은 소프트웨어를 만드는 설계 철학입니다. 손님과 셰프의 역할 분리를 항상 기억하세요. 당신의 클래스가 손님의 역할을 넘어 주방 일까지 하고 있지는 않은지 끊임없이 점검한다면, 훨씬 더 전문적이고 깨끗한 코드를 작성하는 개발자로 성장할 수 있을 것입니다.
