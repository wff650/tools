#php 对文件的 分割及排序
分割文件主要使用fseek 指定指针位置，并且注意不能破坏文件行内容的完整性
排序 先把文件分割成多个已经排序好的小文件，然后进行排序输入到一个文件中
