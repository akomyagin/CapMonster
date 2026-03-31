dockComposer = docker run --rm -ti --volume ./:/var/www/ php81_library composer

build:
	docker build -t php81_library ./

install:
	$(dockComposer) install

update:
	$(dockComposer) update

test:
	$(dockComposer) test
