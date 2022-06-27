# hyperf-laravel
适配laravel，

## 环境
php8+

## 适配
`Hyperf\HttpServer\Request` `Hyperf\HttpServer\Contract\RequestInterface`

增加 only / get 方法 与 laravel用法一致


## 路由适配

#### hyperf 原来路由
```
- 参数定义：(定义了参数后url上的路径必须以 / 结尾，否则不能访问)
    - {id}：必选
    - [{id}]：选填
```
修改 通过 aop切面重写 路由收集器以适配没有 / 时也可以访问

#### 示例

```
Route::get('/api/test/{a}/{b}/[{c}]', function($a, $b, $c=null) {
    var_dump($a, $b, $c);
});
```

#### 请求
* http://localhost:9501/api/test/a/b/c is ok
* http://localhost:9501/api/test/a/b/ is ok
* http://localhost:9501/api/test/a/b is ok 默认hyperf不支持
```
