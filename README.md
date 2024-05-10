## Description
This repository was made to provide a test case for issue https://github.com/doctrine/orm/issues/11448

## Installation and Running
Make sure you have **docker-compose** and **GNU Make** installed.
1. `make up` to build the image and start the containers.
Once the command outputs similar to following, you should be good to go:
```
===== Finished entrypoint script =====
[10-May-2024 17:58:30] NOTICE: fpm is running, pid 1
[10-May-2024 17:58:30] NOTICE: ready to handle connections
```
2. `make case-v2.19.4` to run the previous behavior from `doctrine/orm 2.19.4`
3. `make case-v2.19.5` to run the current behavior from `doctrine/orm 2.19.5`
4. `make down` to stop and remove the containers and its volumes

You can also run `make upd` instead of `make up` to run and build in daemon mode.

### Problem
Following scenario produces a breaking change after upgrading to `doctrine/orm: "2.19.5"`:
1. Create a Parent and Child entity which are linked via `OneToMany` relation. Parent relation is configured to `cascade: ['persist', 'remove']`
2. Delete a Child entity via `$entityManager->remove($child)`
3. Retrieve a Parent entity which is linked to a deleted Child via f.e. `$parentRepository->find()`
4. Create a new Child entity and link it to retrieved Parent entity
5. Persist that Parent entity `$entityManager->persist($parent)`

### Previous Behavior (v2.19.4)
After calling `$entityManager->flush()` Child entity was deleted and a new Child was created and assigned to Parent.
```
docker-compose exec php-testcase bin/console app:test:case
 ---- --------- ----------------------- 
  ID   Book ID   Title                  
 ---- --------- ----------------------- 
  2    1         Page from Fixture      
  4    1         Page from Fixture      
  6    1         Page from Fixture      
  8    1         Page from Fixture      
  10   1         Page from Fixture      
  12   2         Page from Fixture      
  14   2         Page from Fixture      
  16   2         Page from Fixture      
  18   2         Page from Fixture      
  20   2         Page from Fixture      
  22   3         Page from Fixture      
  24   3         Page from Fixture      
  26   3         Page from Fixture      
  28   3         Page from Fixture      
  30   3         Page from Fixture      
  31   1         New Page from Command  
  32   1         New Page from Command  
  33   1         New Page from Command  
  34   1         New Page from Command  
  35   1         New Page from Command  
  36   2         New Page from Command  
  37   2         New Page from Command  
  38   2         New Page from Command  
  39   2         New Page from Command  
  40   2         New Page from Command  
  41   3         New Page from Command  
  42   3         New Page from Command  
  43   3         New Page from Command  
  44   3         New Page from Command  
  45   3         New Page from Command  
 ---- --------- ----------------------- 

```

### Current Behavior (doctrine/orm v2.19.5)
After calling `$entityManager->flush()` Child entity is not deleted and a new Child is created and assigned to parent (remove had no effect)
```
docker-compose exec php-testcase bin/console app:test:case
 ---- --------- ----------------------- 
  ID   Book ID   Title                  
 ---- --------- ----------------------- 
  1    1         Page from Fixture      
  2    1         Page from Fixture      
  3    1         Page from Fixture      
  4    1         Page from Fixture      
  5    1         Page from Fixture      
  6    1         Page from Fixture      
  7    1         Page from Fixture      
  8    1         Page from Fixture      
  9    1         Page from Fixture      
  10   1         Page from Fixture      
  11   2         Page from Fixture      
  12   2         Page from Fixture      
  13   2         Page from Fixture      
  14   2         Page from Fixture      
  15   2         Page from Fixture      
  16   2         Page from Fixture      
  17   2         Page from Fixture      
  18   2         Page from Fixture      
  19   2         Page from Fixture      
  20   2         Page from Fixture      
  21   3         Page from Fixture      
  22   3         Page from Fixture      
  23   3         Page from Fixture      
  24   3         Page from Fixture      
  25   3         Page from Fixture      
  26   3         Page from Fixture      
  27   3         Page from Fixture      
  28   3         Page from Fixture      
  29   3         Page from Fixture      
  30   3         Page from Fixture      
  31   1         New Page from Command  
  32   1         New Page from Command  
  33   1         New Page from Command  
  34   1         New Page from Command  
  35   1         New Page from Command  
  36   2         New Page from Command  
  37   2         New Page from Command  
  38   2         New Page from Command  
  39   2         New Page from Command  
  40   2         New Page from Command  
  41   3         New Page from Command  
  42   3         New Page from Command  
  43   3         New Page from Command  
  44   3         New Page from Command  
  45   3         New Page from Command  
 ---- --------- ----------------------- 
```
### Code Example
Refer to `App\Command\TestCaseCommand` for example scenario where the issue occurs.
