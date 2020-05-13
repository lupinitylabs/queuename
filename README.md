## Proof of Concept: Chaging queue name on queued Listeners

This is to demonstrate that with queued Listeners, the queue property cannot be altered in time of dispatching to influence which queue the resulting job is queued on.

### How to reproduce

When visiting the home route of this project, a [HomepageShown](app/Events/HomepageShown.php) event [is emitted](routes/web.php), which in turn triggers a [QueuesOnSomeQueue](app/Listeners/QueuesOnSomeQueue.php) listener that implements `ShouldQueue` and is thus prepared to be queued.

For simplicity of demonstration, I have left the queue driver at sync, but the same happens with the database driver and due to the reasons outlined below should happen with any queue driver.

The listener just dumps `$this->job` and dies. Therefore, when visiting the home route, you'll see something like:

    Illuminate\Queue\Jobs\SyncJob {#279 ▼
      #job: null
      #payload: "{"uuid":"4f81f0bb-ad08-4850-8ebe-136d71e847a4","displayName":"App\\Listeners\\QueuesOnSomeQueue","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":nu ▶"
      #instance: Illuminate\Queue\CallQueuedHandler {#286 ▶}
      #container: Illuminate\Foundation\Application {#2 ▶}
      #deleted: false
      #released: false
      #failed: false
      #connectionName: "sync"
      #queue: "initial-queue"
    }   

As you can see, the queue is still `initial-queue`, while it is expected to be `destination-queue` since the constructor of the [QueuesOnSomeQueue](app/Listeners/QueuesOnSomeQueue.php) listener reads:

    public function __construct()
    {
        $this->queue = 'destination-queue';
    }


### Why that happens

This is because in [Illuminate\Events\Dispatcher::createListenerAndJob()](https://github.com/laravel/framework/blob/7.x/src/Illuminate/Events/Dispatcher.php#L509), the listener instance is created using

`$listener = (new ReflectionClass($class))->newInstanceWithoutConstructor();`

which essentially creates the instance without calling the constructor. The attributes are then used to set the listener options on the job at [Illuminate\Events\Dispatcher::propagateListenerOptions()](https://github.com/laravel/framework/blob/7.x/src/Illuminate/Events/Dispatcher.php#L523).

In the same method, the `retryAfter` and `timeoutAt` properties on the job object are set via a `retryAfter` or `retryUntil` method, if present. However, there are no such options for the `tries` or `queue` attributes.
