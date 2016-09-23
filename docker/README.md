##选择镜像
docker pull ubuntu
##使用 buildfile创建镜像
docker build --no-cache=true -t myserver buildfile
##启动镜像，绑定端口，-D长时间运行，启动则带有ssh服务
docker -dt -p port:port myserver /usr/sbin/sshd -D
##查看启动的docker 服务
docker ps -a
##查看docker中的images 镜像
docker images
##停止，开启，删除 服务
docker stop|start|rm dockerId
##删除镜像
docker rmi dockerId
##使用exec 命令调用 服务中的命令或服务
docker exec dockerId [commands]
