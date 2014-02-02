<?

namespace Infuso\Core;

/**
 * Класс, реализующий паттерн "Поведение"
 **/
class Behaviour {

	/**
	 * Ссылка на компонент
	 **/
	protected $component;
	
	/**
	 * Порядок добавления поведения в компонент
	 * Устанавливается при добавлении поведения к компоненту и участвует в сортировке поведений
	 **/
	protected $behaviourSequenceNumber = 0;

	/**
	 * При помощи этой функции вы можете прикрепить это поведение как стандартное к любому классу.
	 * @return string Класс, к которому вы хотите приеркпить поведение
	 **/
	public function addToClass() {
		return null;
	}

	/**
	 * @return float Приоритет поведения
	 * Поведения с более высоким приоритетам просматриваются в первую очередь
	 **/
	public function behaviourPriority() {
		return 0;
	}

	/**
	 * @return float Порядковый номер поведения
	 * Используется при сортировке поведений с совпадающими приоритетами
	 * Поведения с большим порядкоывм номером просматриваются в первую очередь
	 **/
	public function behaviourSequenceNumber() {
		return $this->behaviourSequenceNumber;
	}

	/**
	 * При добавлении поведения к классу, вызывается этот метод
	 * Метод сообщает поведению что оно прикреплено к классу
	 * Также поведению передается его порядковый номер, который будет использоваться
	 * при сортировке поведений с совпадающими приоритетами
	 **/
	public final function registerComponent($component,$sequenceNumber) {
		$this->component = $component;
		$this->behaviourSequenceNumber = $sequenceNumber;
	}

	/**
	 * Возвращает объект компонента - объект, к которму прикреплено данное поведение.
	 **/
	public final function component() {
		return $this->component;
	}

	/**
	 * Магический метод
	 * Перенаправляет вызов несуществующего метода поведения в компонент
	 **/
	public final function __call($fn,$params) {
		return call_user_func_array(array($this->component(),$fn),$params);
	}

	/**
	 * Метод определяет, какой метод поведения будет вызван, если у компонента метод $fn отсутствует
	 * По умолчанию вызывается метод с тем же имененм, но это можно изменить
	 **/
	public function routeBehaviourMethod($fn) {
	
	    if(!method_exists($this,$fn)) {
	        return false;
	    }
	        
		// Проверяем, является ли метод которым мы хотим вызвать публичным
	    $class = get_class($this);
	    $reflection = Component::factoryReflectionMethod($class,$fn);
	    if(!$reflection->isPublic()) {
	        return false;
		}
		
		return $fn;
	}

}
