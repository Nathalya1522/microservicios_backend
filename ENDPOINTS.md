# Documentación de Endpoints

## ms-auth
- POST /login - Iniciar sesión
- POST /logout - Cerrar sesión

## ms-reservas
- GET /mesas - Listar mesas
- POST /mesas - Crear mesa
- PUT /mesas/{id} - Editar mesa
- DELETE /mesas/{id} - Eliminar mesa
- GET /reservas - Listar reservas
- POST /reservas - Crear reserva
- PUT /reservas/{id} - Editar reserva
- DELETE /reservas/{id} - Cancelar reserva

## ms-productos
- GET /productos - Listar productos
- POST /productos - Crear producto
- PUT /productos/{id} - Editar producto
- DELETE /productos/{id} - Eliminar producto

## ms-pedidos
- GET /pedidos - Listar pedidos
- POST /pedidos - Crear pedido
- PUT /pedidos/{id} - Cambiar estado
- DELETE /pedidos/{id} - Cancelar pedido